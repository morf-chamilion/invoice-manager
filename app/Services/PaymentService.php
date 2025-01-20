<?php

namespace App\Services;

use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Vendor;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Collection;

class PaymentService extends BaseService
{
	public function __construct(
		private PaymentRepository $paymentRepository,
		private SettingService $settingService,
		private CustomerService $customerService,
		private InvoiceService $invoiceService,
	) {
		parent::__construct($paymentRepository);
	}

	/**
	 * Get the authenticated vendor.
	 */
	public function getAuthVendor(): ?Vendor
	{
		if ($this->getAdminAuthUser()->vendor) {
			return $this->getAdminAuthUser()->vendor;
		}

		return null;
	}

	/**
	 * Get all payments.
	 */
	public function getAllPayments(): Collection
	{
		return $this->paymentRepository->getAll();
	}

	/**
	 * Create a new payment.
	 */
	public function createPayment(array $attributes): Payment
	{
		if ($this->getAdminAuthUser()) {
			$attributes['created_by'] = $this->getAdminAuthUser()->id;
			$attributes['vendor_id'] = $this->getAuthVendor()->id;
		}

		$payment = $this->paymentRepository->create($attributes);
		$invoice = $payment->invoice;

		$this->handleInvoiceStatus($invoice, $payment);

		return $payment;
	}

	/**
	 * Get the specified payment.
	 */
	public function getPayment(int $paymentId): ?Payment
	{
		return $this->paymentRepository->getById($paymentId);
	}

	/**
	 * Get the specified payment attribute.
	 */
	public function getPaymentWhere(string $columnName, mixed $value): ?Payment
	{
		return $this->paymentRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Delete a specific payment.
	 */
	public function deletePayment(int $paymentId): int
	{
		return $this->paymentRepository->delete($paymentId);
	}

	/**
	 * Update an existing payment.
	 */
	public function updatePayment(int $paymentId, array $newAttributes): bool
	{
		if ($this->getAdminAuthUser()) {
			$newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
		}

		$updated = $this->paymentRepository->update($paymentId, $newAttributes);

		$payment = $this->getPayment($paymentId);
		$invoice = $payment->invoice;

		$this->handleInvoiceStatus($invoice, $payment);

		return $updated;
	}

	/**
	 * Handle invoice payment status update.
	 */
	protected function handleInvoiceStatus(?Invoice $invoice, ?Payment $payment): void
	{
		if (!$invoice || !$payment) return;

		if ($payment->status != PaymentStatus::DECLINED) {
			$totalPaid = $invoice->payments->where('status', PaymentStatus::PAID)->sum('amount');

			$paymentStatus = $totalPaid >= $invoice->total_price ?
				InvoicePaymentStatus::PAID : InvoicePaymentStatus::PARTIALLY_PAID;

			$invoice->update([
				'payment_status' => $paymentStatus,
				'status' => InvoiceStatus::COMPLETED
			]);
		}
	}

	/**
	 * Get all customers.
	 */
	public function getAllCustomers(): Collection
	{
		return $this->customerService->getAllActiveCustomers(
			$this->getAuthVendor(),
		);
	}

	/**
	 * Get all invoices.
	 */
	public function getAllInvoices(int|null $customerId = null): Collection
	{
		$customer = $customerId ? $this->customerService->getCustomer($customerId) : null;

		return $this->invoiceService->getAllActiveInvoices(
			$this->getAuthVendor(),
			$customer
		);
	}
}
