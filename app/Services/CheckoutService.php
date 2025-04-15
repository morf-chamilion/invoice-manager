<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Notifications\Checkout\CheckoutSuccessAdminNotification;
use App\Notifications\Checkout\CheckoutSuccessCustomerNotification;
use App\Services\PaymentGateways\CyberSourcePaymentGateway;
use Illuminate\Support\Carbon;
use Exception;

class CheckoutService extends CyberSourcePaymentGateway
{
	public function __construct(
		private SettingService $settingService,
		private PaymentService $paymentService,
	) {}

	/**
	 * Update payment data based on the transaction.
	 */
	public function updateCardPaymentData(
		string $transactionId,
		int $paymentId,
		float $amount,
		PaymentStatus $paymentStatus,
	): ?Payment {
		$attributes = [
			'data->transaction_id' => $transactionId,
			'data->amount' => $amount,
			'status' => $paymentStatus,
			'date' => Carbon::now(),
		];

		if ($paymentStatus) {
			$attributes['status'] = $paymentStatus;
		}

		$updated = $this->paymentService->updatePayment($paymentId, $attributes);

		if (!$updated) {
			return null;
		}

		return $this->paymentService->getPayment($paymentId);
	}

	/**
	 * Handle payment success mail notfications.
	 */
	public function paymentSuccessMailNotification(Payment $payment): bool|Exception
	{
		try {
			$payment->notify(new CheckoutSuccessAdminNotification($payment));
			$payment->notify(new CheckoutSuccessCustomerNotification($payment));

			return true;
		} catch (Exception $e) {
			return $e;
		}

		return false;
	}
}
