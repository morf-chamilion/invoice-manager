<?php

namespace App\Notifications\RecurringInvoice;

use App\Enums\SettingModule;
use App\Mail\CommonMail;
use App\Models\Invoice;
use App\Models\RecurringInvoice;
use App\Notifications\BaseNotification;
use App\RoutePaths\Admin\Invoice\InvoiceRoutePath;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;

class RecurringInvoiceDraftReadyNotification extends BaseNotification
{
    use Queueable;

    public function __construct(
        protected Invoice $invoice,
        protected RecurringInvoice $recurringInvoice,
    ) {}

    /**
     * Get mail settings.
     */
    protected function mailSettings(): SettingService
    {
        /** @var SettingService $settingService */
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
        return new CommonMail(
            mailSubject: $this->formatMailContent($this->mailSubject(), $this->mailContent()),
            mailBody: $this->formatMailContent($this->mailBody(), $this->mailContent()),
        );
    }

    /**
     * Mail template content.
     */
    protected function mailContent(): array
    {
        return [
            '[invoice_number]' => $this->invoice->number,
            '[recurring_invoice_number]' => $this->recurringInvoice->number,
            '[customer_name]' => $this->invoice->customer->name,
            '[invoice_date]' => $this->invoice->readableDate,
            '[invoice_due_date]' => $this->invoice->readableDueDate,
            '[invoice_amount]' => $this->invoice->readableTotalPrice,
            '[edit_invoice_link]' => Blade::render('<a href="{{ $link }}" class="button button-primary">{{ $title }}</a>', [
                'title' => __('Edit Invoice'),
                'link' => route(InvoiceRoutePath::EDIT, $this->invoice->id),
            ]),
        ];
    }

    /**
     * Mail subject.
     */
    protected function mailSubject(): ?string
    {
        return "Draft Invoice Generated: {$this->invoice->number} - Ready for Review";
    }

    /**
     * Mail body.
     */
    protected function mailBody(): ?string
    {
        $lines = [
            'A draft invoice has been generated from recurring invoice.',
            'Invoice Number: [invoice_number]',
            'Customer: [customer_name]',
            'Date: [invoice_date]',
            'Due Date: [invoice_due_date]',
            'Amount: [invoice_amount]',
            'Please review and send the invoice:',
            '[edit_invoice_link]',
        ];

        return implode("\n", $lines);
    }
}
