<?php

namespace App\Notifications\Customer;

use App\Mail\PasswordResetMail;
use App\Notifications\BaseNotification;
use App\RoutePaths\Front\Auth\AuthRoutePath;
use Closure;

class CustomerResetPasswordNotification extends BaseNotification
{
	/**
	 * The password reset token.
	 */
	public string $token;

	/**
	 * The callback that should be used to create the reset password URL.
	 */
	public static ?Closure $createUrlCallback;

	/**
	 * Create a notification instance.
	 */
	public function __construct(string $token)
	{
		$this->token = $token;
	}

	/**
	 * Get the notification's channels.
	 */
	public function via(mixed $notifiable): array|string
	{
		return ['mail'];
	}

	/**
	 * Build the mail representation of the notification.
	 */
	public function toMail(mixed $notifiable): PasswordResetMail
	{
		return (new PasswordResetMail(
			mailSubject: $this->mailSubject(),
			mailBody: [
				'passwordResetUrl' => $this->resetUrl($notifiable),
			],
		))->to($notifiable->getEmailForPasswordReset());
	}

	/**
	 * Get the reset URL for the given notifiable.
	 *
	 * @param  mixed  $notifiable
	 * @return string
	 */
	protected function resetUrl($notifiable)
	{
		return url(route(AuthRoutePath::PASSWORD_RESET, [
			'token' => $this->token,
			'email' => $notifiable->getEmailForPasswordReset(),
		], false));
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): string
	{
		return __('Customer Reset Password Notification');
	}
}
