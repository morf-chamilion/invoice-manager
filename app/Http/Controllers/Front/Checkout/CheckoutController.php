<?php

namespace App\Http\Controllers\Front\Checkout;

use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Enums\SettingModule;
use App\Logs\CheckoutLogger;
use App\Messages\CheckoutMessage;
use App\Http\Controllers\Front\FrontBaseController;
use App\Http\Requests\Front\Checkout\PaymentGateways\CyberSourceCallbackRequest;
use App\Providers\CustomerServiceProvider;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;
use App\RoutePaths\Front\Page\PageRoutePath;
use App\Services\InvoiceService;
use App\Services\CheckoutService;
use App\Services\SettingService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class CheckoutController extends FrontBaseController
{
	public function __construct(
		private SettingService $settingService,
		private CheckoutService $checkoutService,
		private CheckoutMessage $checkoutMessage,
		private InvoiceService $invoiceService,
	) {
		parent::__construct(settingService: $settingService);
	}

	/**
	 * Show the checkout form.
	 */
	public function show(string $id): View|RedirectResponse
	{
		try {
			$sessionId = Crypt::decryptString($id);
		} catch (DecryptException) {
			return redirect()->route(CheckoutRoutePath::FAILURE_SHOW)
				->withErrors('Session ID is invalid or expired.');
		}

		$invoice = $this->invoiceService->getInvoice($sessionId);

		if (CustomerServiceProvider::getAuthUser()->id !== $invoice->customer->id) {
			return redirect()->route(CheckoutRoutePath::FAILURE_SHOW, $id)
				->withErrors('You are not authorized to view this invoice.');
		}

		if ($invoice->status === InvoiceStatus::DRAFT) {
			return redirect()->route(CheckoutRoutePath::FAILURE_SHOW, $id)
				->withErrors('Invoice drafted.');
		}

		if (
			$invoice->payment_status === InvoicePaymentStatus::PAID ||
			Carbon::parse($invoice->payment_date)?->isFuture()
		) {
			return redirect()->route(CheckoutRoutePath::SUCCESS_SHOW, $id);
		}

		$this->sharePageData(SettingModule::GENERAL);

		return view(CheckoutRoutePath::SHOW)->with([
			'checkoutFields' => $this->checkoutService->getCheckoutData($invoice),
			'checkoutGatewayUrl' => $this->checkoutService->checkoutGatewayUrl(),
			'invoice' => $invoice,
		]);
	}

	/**
	 * Store success callback data in storage.
	 */
	public function store(string $id, CyberSourceCallbackRequest $request): RedirectResponse
	{
		try {
			$sessionId = Crypt::decryptString($id);
		} catch (DecryptException) {
			return redirect()->route(CheckoutRoutePath::FAILURE_SHOW, $id)
				->withErrors($this->checkoutMessage->sessionInvalid());
		}

		$attributes = $request->getAttributes();

		CheckoutLogger::info($this->checkoutMessage->checkoutSessionCreate(), [
			'sessionId' => $sessionId,
			'request' => $attributes,
		]);

		if (
			$attributes['reason_code'] != $this->checkoutService::GATEWAY_SUCCESS_CODE
		) {
			$invoice = $this->checkoutService->updateCardPaymentData(
				transactionId: $attributes['transaction_id'],
				invoiceId: $sessionId,
				amount: $attributes['auth_amount'],
				paymentStatus: InvoicePaymentStatus::DECLINED,
			);

			CheckoutLogger::error($this->checkoutMessage->paymentGatewayTransactionFailure(), [
				'sessionId' => $sessionId,
				'request' => $attributes,
			]);

			return redirect()->route(CheckoutRoutePath::FAILURE_SHOW, $id)
				->withErrors($this->checkoutMessage->paymentGatewayTransactionFailure());
		}

		$invoice = $this->checkoutService->updateCardPaymentData(
			transactionId: $attributes['transaction_id'],
			invoiceId: $sessionId,
			amount: $attributes['auth_amount'],
			paymentStatus: InvoicePaymentStatus::PAID,
			invoiceStatus: InvoiceStatus::COMPLETED,
		);

		if (!$invoice) {
			CheckoutLogger::error($this->checkoutMessage->checkoutUpdateFailure(), [
				'sessionId' => $sessionId,
				'request' => $attributes,
			]);

			return redirect()->route(CheckoutRoutePath::FAILURE_SHOW, $id)
				->withErrors($this->checkoutMessage->checkoutFailure());
		}

		$this->checkoutService->paymentSuccessMailNotification($invoice);

		CheckoutLogger::info($this->checkoutMessage->checkoutSuccess(), [
			'sessionId' => $sessionId,
			'status' => InvoicePaymentStatus::PAID->getName(),
			'request' => $attributes,
		]);

		return redirect()->route(CheckoutRoutePath::SUCCESS_SHOW, $id);
	}

	/**
	 * Destroy checkout session data.
	 */
	public function destroy(): RedirectResponse
	{
		return redirect()->route(PageRoutePath::HOME);
	}
}
