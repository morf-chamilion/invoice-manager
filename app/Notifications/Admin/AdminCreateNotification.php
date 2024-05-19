<?php

namespace App\Notifications\Admin;

use App\Enums\SettingModule;
use App\Mail\CommonMail;
use App\Models\User;
use App\Notifications\BaseNotification;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;

class AdminCreateNotification extends BaseNotification
{
	use Queueable;

	protected $userPasswordPlainText;

	public function __construct(
		protected User $user,
		#[\SensitiveParameter]
		string $userPasswordPlainText,
	) {
		$this->userPasswordPlainText = $userPasswordPlainText;
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
	public function toMail(mixed $notifiable): CommonMail
	{
		return (new CommonMail(
			mailSubject: $this->formatMailContent($this->mailSubject(), $this->mailContent()),
			mailBody: $this->formatMailContent($this->mailBody(), $this->mailContent()),
		))->to($this->user->email);
	}

	/**
	 * Mail template content.
	 */
	protected function mailContent(): array
	{
		return [
			'[name]' => $this->user->name,
			'[email]' => $this->user->email,
			'[password]' => $this->userPasswordPlainText,
			'[verification_link]' => Blade::render('<a href="{{ $link }}" class="button button-primary">{{ $title }}</a>', [
				'title' => 'Verify Account',
				'link' => $this->user->verificationUrl(),
			]),
		];
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): ?string
	{
		return $this->mailSettings()->get('user_admin_create_mail_subject');
	}

	/**
	 * Mail body.
	 */
	protected function mailBody(): ?string
	{
		return $this->mailSettings()->get('user_admin_create_mail_template');
	}
}
