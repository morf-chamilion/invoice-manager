<?php

namespace App\Notifications\Customer;

use App\Enums\SettingModule;
use App\Mail\CommonMail;
use App\Models\Customer;
use App\Notifications\BaseNotification;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;

class CustomerCreateNotification extends BaseNotification
{
	use Queueable;

	protected $customerPasswordPlainText;

	public function __construct(
		protected Customer $customer,
		#[\SensitiveParameter]
		string $customerPasswordPlainText,
	) {
		$this->customerPasswordPlainText = $customerPasswordPlainText;
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
		))->to($this->customer->email);
	}

	/**
	 * Mail template content.
	 */
	protected function mailContent(): array
	{
		return [
			'[name]' => $this->customer->name,
			'[email]' => $this->customer->email,
			'[password]' => $this->customerPasswordPlainText,
			'[verification_link]' => Blade::render('<a href="{{ $link }}" class="button button-primary">{{ $title }}</a>', [
				'title' => 'Verify Account',
				'link' => $this->customer->verificationUrl(),
			]),
		];
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): ?string
	{
		return $this->mailSettings()->get('user_customer_create_mail_subject');
	}

	/**
	 * Mail body.
	 */
	protected function mailBody(): ?string
	{
		return $this->mailSettings()->get('user_customer_create_mail_template');
	}
}
