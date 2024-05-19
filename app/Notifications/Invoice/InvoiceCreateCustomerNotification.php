<?php

namespace App\Notifications\Invoice;

use App\Enums\SettingModule;
use App\Mail\CommonMail;
use App\Models\Invoice;
use App\Notifications\BaseNotification;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;

class InvoiceCreateCustomerNotification extends BaseNotification
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
	public function toMail(mixed $notifiable): CommonMail
	{
		return (new CommonMail(
			mailSubject: $this->formatMailContent($this->mailSubject(), $this->mailContent()),
			mailBody: $this->formatMailContent($this->mailBody(), $this->mailContent()),
		))->to($this->invoice->customer->email);
	}

	/**
	 * Mail template content.
	 */
	protected function mailContent(): array
	{
		return [
			'[invoice_number]' => $this->invoice->number,
			'[customer_name]' => $this->invoice->customer->name,
			'[invoice_due_date]' => $this->invoice->readableDueDate,
			'[payment_link]' => Blade::render('<a href="{{ $link }}" class="button button-primary">{{ $title }}</a>', [
				'link' => $this->invoice->checkout_link,
				'title' => __('Pay Now'),
			]),
			'[invoice_link]' => Blade::render('<a href="{{ $link }}" class="button button-primary">{{ $title }}</a>', [
				'link' => route(CustomerRoutePath::INVOICE_SHOW, $this->invoice->id),
				'title' => __('View Invoice'),
			]),
		];
	}

	/**
	 * Mail subject.
	 */
	protected function mailSubject(): ?string
	{
		return $this->mailSettings()->get('invoice_create_customer_mail_subject');
	}

	/**
	 * Mail body.
	 */
	protected function mailBody(): ?string
	{
		return $this->mailSettings()->get('invoice_create_customer_mail_template');
	}
}
