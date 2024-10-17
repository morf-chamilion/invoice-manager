<?php

namespace App\Notifications\Quotation;

use App\Enums\SettingModule;
use App\Mail\CommonMail;
use App\Models\Quotation;
use App\Notifications\BaseNotification;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;

class QuotationCreateCustomerNotification extends BaseNotification
{
	use Queueable;

	public function __construct(
		protected Quotation $quotation,
	) {}

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
		))->to($this->quotation->customer->email);
	}

	/**
	 * Mail template content.
	 */
	protected function mailContent(): array
	{
		return [
			'[quotation_number]' => $this->quotation->number,
			'[customer_name]' => $this->quotation->customer->name,
			'[quotation_due_date]' => $this->quotation->readableDueDate,
			'[payment_link]' => Blade::render('<a href="{{ $link }}" class="button button-primary">{{ $title }}</a>', [
				'link' => $this->quotation->checkout_link,
				'title' => __('Pay Now'),
			]),
		];
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): ?string
	{
		return $this->mailSettings()->get('quotation_create_customer_mail_subject');
	}

	/**
	 * Mail body.
	 */
	protected function mailBody(): ?string
	{
		return $this->mailSettings()->get('quotation_create_customer_mail_template');
	}
}
