<?php

namespace App\Notifications\Customer;

use App\Mail\VerificationMail;
use App\Models\Customer;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

class CustomerVerifyNotification extends BaseNotification
{
	use Queueable;

	public function __construct(
		protected Customer $customer,
	) {
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
	public function toMail(mixed $notifiable): VerificationMail
	{
		return (new VerificationMail(
			mailSubject: $this->mailSubject(),
			mailBody: [
				'verificationUrl' => $this->customer->verificationUrl(),
			],
		))->to($this->customer->email);
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): ?string
	{
		return "Verify Your Email Address {$this->customer->name}";
	}
}
