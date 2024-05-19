<?php

namespace App\Notifications\Checkout;

use App\Enums\SettingModule;
use App\Mail\PaymentReceiptMail;
use App\Models\Invoice;
use App\Notifications\BaseNotification;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;

class CheckoutSuccessCustomerNotification extends BaseNotification
{
	use Queueable;

	public function __construct(
		protected Invoice $invoice,
	) {
	}

	/**
	 * Get mail settings.
	 */
	protected function mailSettings(): SettingService
	{
		/** @var SettingService $settingService  */
		$settingService = App::make(SettingService::class);

		return $settingService->module(SettingModule::MAIL);
	}

	/**
	 * Get the notification's channels.
	 */
	public function via(mixed $notifiable): string|array
	{
		return ['mail'];
	}

	/**
	 * Build the mail representation of the notification.
	 */
	public function toMail(mixed $notifiable): PaymentReceiptMail
	{
		return (new PaymentReceiptMail(
			mailSubject: $this->mailSubject(),
			mailBody: [
				'invoice' => $this->invoice,
			],
		))->to($this->invoice->customer->email);
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): ?string
	{
		return 'Your Payment Success';
	}
}
