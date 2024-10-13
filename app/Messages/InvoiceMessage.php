<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\InvoiceService;

class InvoiceMessage extends BaseMessage
{
	public function __construct(
		protected InvoiceService $invoiceService,
	) {}

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

	/**
	 * Get all customers success message.
	 */
	public function getAllCustomersSuccess(): string
	{
		return 'Sucessfully got all customers.';
	}

	/**
	 * Get all customers failed message.
	 */
	public function getAllCustomersFailed(): string
	{
		return 'An error occured while getting all the customers';
	}

	/**
	 * Create customer success message.
	 */
	public function createCustomerSuccess(): string
	{
		return "Successfully created a new customer.";
	}

	/**
	 * Create customer failed message.
	 */
	public function createCustomerFailed(): string
	{
		return "An error occurred while saving this customer.";
	}
}
