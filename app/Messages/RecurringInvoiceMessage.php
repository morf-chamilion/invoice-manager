<?php

namespace App\Messages;

use App\Services\RecurringInvoiceService;

class RecurringInvoiceMessage extends BaseMessage
{
    public function __construct(
        protected RecurringInvoiceService $recurringInvoiceService,
    ) {}

    protected function modelName(): string
    {
        return $this->recurringInvoiceService->modelName();
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
        return 'Successfully created a new customer.';
    }

    /**
     * Create customer failed message.
     */
    public function createCustomerFailed(): string
    {
        return 'An error occurred while saving this customer.';
    }
}
