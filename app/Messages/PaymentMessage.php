<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\PaymentService;

class PaymentMessage extends BaseMessage
{
	public function __construct(
		protected PaymentService $paymentService,
	) {}

	protected function modelName(): string
	{
		return $this->paymentService->modelName();
	}

	/**
	 * Get all invoices success message.
	 */
	public function getAllInvoicesSuccess(): string
	{
		return 'Sucessfully got all invoices.';
	}

	/**
	 * Get all invoices failed message.
	 */
	public function getAllInvoicesFailed(): string
	{
		return 'An error occured while getting all the invoices.';
	}
}
