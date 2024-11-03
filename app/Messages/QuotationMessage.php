<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\QuotationService;

class QuotationMessage extends BaseMessage
{
	public function __construct(
		protected QuotationService $quotationService,
	) {}

	protected function modelName(): string
	{
		return $this->quotationService->modelName();
	}

	/**
	 * Quotation update incompatible status message.
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

	/**
	 * Generate invoice success message.
	 */
	public function generateInvoiceSuccess(): string
	{
		return "Successfully created a new invoice.";
	}

	/**
	 * Generate invoice failed message.
	 */
	public function generateInvoiceFailed(): string
	{
		return "An error occurred while creating a new invoice.";
	}
}
