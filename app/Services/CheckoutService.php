<?php

namespace App\Services;

use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Notifications\Checkout\CheckoutSuccessAdminNotification;
use App\Notifications\Checkout\CheckoutSuccessCustomerNotification;
use App\Services\PaymentGateways\CyberSourcePaymentGateway;
use Illuminate\Support\Carbon;
use Exception;

class CheckoutService extends CyberSourcePaymentGateway
{
	public function __construct(
		private SettingService $settingService,
		private InvoiceService $invoiceService,
	) {
	}

	/**
	 * Update payment data based on the transaction.
	 */
	public function updateCardPaymentData(
		string $transactionId,
		int $invoiceId,
		float $amount,
		InvoicePaymentStatus $paymentStatus,
		?InvoiceStatus $invoiceStatus = null,
	): ?Invoice {
		$attributes = [
			'payment_data->transaction_id' => $transactionId,
			'payment_data->amount' => $amount,
			'payment_status' => $paymentStatus,
			'payment_date' => Carbon::today()->format('Y-m-d'),
		];

		if ($invoiceStatus) {
			$attributes['status'] = $invoiceStatus;
		}

		$updated = $this->invoiceService->updateInvoice($invoiceId, $attributes);

		if (!$updated) {
			return null;
		}

		return $this->invoiceService->getInvoice($invoiceId);
	}

	/**
	 * Handle payment success mail notfications.
	 */
	public function paymentSuccessMailNotification(Invoice $invoice): bool|Exception
	{
		try {
			$invoice->notify(new CheckoutSuccessAdminNotification($invoice));
			$invoice->notify(new CheckoutSuccessCustomerNotification($invoice));

			return true;
		} catch (Exception $e) {
			return $e;
		}

		return false;
	}
}
