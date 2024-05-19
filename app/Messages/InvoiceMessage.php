<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\InvoiceService;

class InvoiceMessage extends BaseMessage
{
	public function __construct(
		protected InvoiceService $invoiceService,
	) {
	}

	protected function modelName(): string
	{
		return $this->invoiceService->modelName();
	}

	/**
	 * Overdue mail success message.
	 */
	public function overdueMailSuccess(): string
	{
		return 'The invoice overdue mail has been sent.';
	}

	/**
	 * Overdue mail failure message.
	 */
	public function overdueMailFailed(): string
	{
		return 'Failed to send the invoice overdue mail.';
	}

	/**
	 * Invoice update incompatible status message.
	 */
	public function incompatibleStatus(): string
	{
		return 'Incompatible status, requires payment.';
	}
}
