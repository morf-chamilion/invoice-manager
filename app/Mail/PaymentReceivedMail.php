<?php

namespace App\Mail;

use App\Enums\SettingModule;
use App\RoutePaths\Mail\MailRoutePath;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\App;

class PaymentReceivedMail extends Mailable
{
	use Queueable, SerializesModels;

	public SettingService $settingService;

	/**
	 * Create a new message instance.
	 */
	public function __construct(
		public string $mailSubject,
		public array $mailBody,
	) {
		$this->settingService = App::make(SettingService::class);
	}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(
			from: new Address(
				Env::get('MAIL_FROM_ADDRESS'),
				$this->settingService->module(SettingModule::MAIL)
					->get('site_name') ?? Env::get('APP_NAME')
			),
			subject: $this->mailSubject,
		);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(
			view: MailRoutePath::RECEIVED,
			with: $this->mailBody,
		);
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
	 */
	public function attachments(): array
	{
		return [];
	}
}
