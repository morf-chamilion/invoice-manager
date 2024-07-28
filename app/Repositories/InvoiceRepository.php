<?php

namespace App\Repositories;

use App\Enums\InvoicePaymentMethod;
use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class InvoiceRepository extends BaseRepository
{
	public function __construct(
		private Invoice $invoice,
	) {
		parent::__construct($invoice);
	}

	/**
	 * Get all invoices.
	 */
	public function getAll(): Collection
	{
		return $this->invoice::all();
	}

	/**
	 * Get the specified invoice.
	 */
	public function getById(int $invoiceId): ?Invoice
	{
		return $this->invoice::find($invoiceId);
	}

	/**
	 * Get the invoice by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?Invoice
	{
		return $this->invoice::where($columnName, $value)->first();
	}

	/**
	 * Get invoice that belongs to a customer.
	 */
	public function getCustomerInvoice(int $invoiceId, int $customerId): ?Invoice
	{
		return $this->invoice->where('id', $invoiceId)
			->where('customer_id', $customerId)
			->where('status', '!=', InvoiceStatus::DRAFT)
			->first();
	}

	/**
	 * Get invoices that belongs to a customer.
	 */
	public function getCustomerInvoices(int $customerId): ?Collection
	{
		return $this->invoice->where('customer_id', $customerId)
			->where('status', '!=', InvoiceStatus::DRAFT)
			->get();
	}

	/**
	 * Get the last invoice for the vendor.
	 */
	public function getLastVendorInvoice(int $vendorId): ?Invoice
	{
		return $this->invoice->where('vendor_id', $vendorId)
			->orderBy('vendor_invoice_number', 'desc')
			->first();
	}

	/**
	 * Delete a specific invoice.
	 */
	public function delete(int $invoiceId): bool|QueryException
	{
		$invoice = $this->getById($invoiceId);

		$this->checkModelHasParentRelations($invoice);

		try {
			return $invoice->delete($invoiceId);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage());

			return false;
		}
	}

	/**
	 * Create a new invoice.
	 */
	public function create(array $attributes): Invoice
	{
		$invoiceItems = Arr::pull($attributes, 'invoice_items');
		$paymentReference = Arr::pull($attributes, 'payment_reference');

		$invoice = $this->invoice::create($attributes);

		$lastInvoice = $this->getLastVendorInvoice($attributes['vendor_id']);
		$invoice->vendor_invoice_number = $lastInvoice ? ++$lastInvoice->vendor_invoice_number : 1;
		$invoice->number = $invoice->id;

		if (
			isset($attributes['payment_method']) &&
			$attributes['payment_method'] == InvoicePaymentMethod::CASH->value
		) {
			$paymentData = $invoice->payment_data;
			$paymentData['transaction_id'] = $invoice->number;
			$paymentData['amount'] = $invoice->total_price;
			$paymentData['reference'] = $paymentReference;

			$invoice->payment_data = $paymentData;
			$invoice->payment_status = InvoicePaymentStatus::PAID;
			$invoice->payment_date = Carbon::today()->format('Y-m-d');
			$invoice->status = InvoiceStatus::COMPLETED;
		}

		$invoice->save();

		if ($invoiceItems) {
			$invoice->invoiceItems()->delete();
			$invoice->invoiceItems()->createMany($invoiceItems);
		}

		return $invoice;
	}

	/**
	 * Update an existing invoice.
	 */
	public function update(int $invoiceId, array $newAttributes): bool
	{
		$invoiceItems = Arr::pull($newAttributes, 'invoice_items');
		$paymentReference = Arr::pull($newAttributes, 'payment_reference');

		$invoice = $this->invoice::findOrFail($invoiceId);

		if (
			isset($newAttributes['payment_method']) &&
			$newAttributes['payment_method'] == InvoicePaymentMethod::CASH->value
		) {
			$paymentData = $invoice->payment_data;
			$paymentData['transaction_id'] = $invoice->number;
			$paymentData['amount'] = $invoice->total_price;
			$paymentData['reference'] = $paymentReference;

			$newAttributes['payment_data'] = $paymentData;
			$newAttributes['payment_status'] = InvoicePaymentStatus::PAID;
			$newAttributes['payment_date'] = Carbon::today()->format('Y-m-d');
			$newAttributes['status'] = InvoiceStatus::COMPLETED;
		}

		$updated = $invoice->update($newAttributes);

		if ($invoiceItems) {
			$invoice->invoiceItems()->delete();
			$invoice->invoiceItems()->createMany($invoiceItems);
		}

		return $updated;
	}
}
