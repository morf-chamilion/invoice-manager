<?php

namespace App\Notifications\Checkout;

use App\Enums\SettingModule;
use App\Mail\PaymentReceivedMail;
use App\Models\Invoice;
use App\Notifications\BaseNotification;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class CheckoutSuccessAdminNotification extends BaseNotification
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
	public function toMail(mixed $notifiable): ?PaymentReceivedMail
	{
		$notificationMails = Collection::make(
			$this->mailSettings()->get('notifications')
		)->pluck('email')->toArray();

		return (new PaymentReceivedMail(
			mailSubject: $this->mailSubject(),
			mailBody: [
				'invoice' => $this->invoice,
			],
		))->to($notificationMails);
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): ?string
	{
		return "Payment Received by {$this->invoice->customer->name}";
	}
}
