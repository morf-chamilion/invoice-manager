<?php

namespace App\Notifications\Admin;

use App\Mail\VerificationMail;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

class AdminVerifyNotification extends BaseNotification
{
	use Queueable;

	public function __construct(
		protected User $user,
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
				'verificationUrl' => $this->user->verificationUrl(),
			],
		))->to($this->user->email);
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): ?string
	{
		return "Verify Your Email Address {$this->user->name}";
	}
}
