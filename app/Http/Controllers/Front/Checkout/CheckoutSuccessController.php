<?php

namespace App\Http\Controllers\Front\Checkout;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;
use App\Services\InvoiceService;
use App\Services\SettingService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;

class CheckoutSuccessController extends FrontBaseController
{
	public function __construct(
		private SettingService $settingService,
		private InvoiceService $invoiceService,
	) {
		parent::__construct(settingService: $settingService);
	}

	/**
	 * Show checkout success view.
	 */
	public function show(string $id): View|RedirectResponse
	{
		try {
			$invoiceId = Crypt::decryptString($id);
		} catch (DecryptException) {
			return redirect()->route(CheckoutRoutePath::FAILURE_SHOW)
				->withErrors('Session ID is invalid or expired.');
		}

		$invoice = $this->invoiceService->getInvoice($invoiceId);

		$this->sharePageData(SettingModule::GENERAL);

		if (empty($invoice->payment_data)) {
			return redirect()->route(CheckoutRoutePath::FAILURE_SHOW, $id)
				->withErrors('Payment data not found.');
		}

		return view(CheckoutRoutePath::SUCCESS_SHOW)->with([
			'invoice' => $invoice,
		]);
	}
}
