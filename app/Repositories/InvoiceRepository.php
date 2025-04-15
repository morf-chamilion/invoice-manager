<?php

namespace App\Repositories;

use App\Enums\InvoiceItemType;
use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Vendor;
use App\Services\MediaService;
use App\Services\Traits\HandlesMedia;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class InvoiceRepository extends BaseRepository
{
	use HandlesMedia;

	public function __construct(
		private Invoice $invoice,
		private MediaService $mediaService,
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
	 * Get all active customers.
	 */
	public function getAllActive(Vendor $vendor = null, Customer $customer = null): Collection
	{
		$query = $this->invoice->where('status', InvoiceStatus::ACTIVE);

		if ($vendor) {
			$query->where('vendor_id', $vendor->id);
		}

		if ($customer) {
			$query->where('customer_id', $customer->id);
		}

		return $query->get();
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

		$invoice = $this->invoice::create($attributes);
		$invoice->vendor()->associate($attributes['vendor_id']);

		$lastInvoice = $this->getLastVendorInvoice($attributes['vendor_id']);
		$invoice->vendor_invoice_number = $lastInvoice ? ++$lastInvoice->vendor_invoice_number : 1;
		$invoice->number = $invoice->id;
		$invoice->discount_type = $attributes['discount_type'];
		$invoice->discount_value = $attributes['discount_value'];

		$totalPrice = 0;

		if ($invoiceItems) {
			$invoice->invoiceItems()->delete();

			$totalPrice = $this->syncInvoiceItems($invoice, $invoiceItems);
			$invoice->total_price = $totalPrice;
		}

		$invoice->total_price = $totalPrice;

		$invoice->save();

		return $invoice;
	}

	/**
	 * Update an existing invoice.
	 */
	public function update(int $invoiceId, array $newAttributes): bool
	{
		$invoiceItems = Arr::pull($newAttributes, 'invoice_items');

		$invoice = $this->invoice::findOrFail($invoiceId);

		$updated = $invoice->update($newAttributes);

		$totalPrice = 0;

		if ($invoiceItems) {
			$totalPrice = $this->syncInvoiceItems($invoice, $invoiceItems);
			$invoice->total_price = $totalPrice;

			$invoice->save();
		} else {
			$invoice->invoiceItems()->delete();
		}

		return $updated;
	}

	/**
	 * Update invoice items and calculate total price.
	 */
	private function syncInvoiceItems($invoice, array|object $invoiceItems): float
	{
		$invoice->invoiceItems()->delete();

		$totalPrice = 0;

		foreach ($invoiceItems as $invoiceItem) {
			$item = new InvoiceItem;
			$item->invoice_id = $invoice->id;

			$this->setInvoiceItemType($item, $invoiceItem);

			$item->description = is_array($invoiceItem) ? $invoiceItem['description'] : $invoiceItem->description;
			$item->quantity = is_array($invoiceItem) ? $invoiceItem['quantity'] : $invoiceItem->quantity;
			$item->unit_price = is_array($invoiceItem) ? $invoiceItem['unit_price'] : $invoiceItem->unit_price;
			$item->amount = is_array($invoiceItem) ? $invoiceItem['amount'] : $invoiceItem->amount;

			$item->save();

			$totalPrice += $item->amount;
		}

		return $this->applyDiscount($totalPrice, $invoice->discount_value ?? 0, $invoice->discount_type ?? 0);
	}

	/**
	 * Set the item type for an invoice item.
	 */
	private function setInvoiceItemType(InvoiceItem $item, array|object $invoiceItem)
	{
		$typeId = is_array($invoiceItem) ? $invoiceItem['type_id'] : $invoiceItem->type_id;
		$itemTitle = is_array($invoiceItem) ? $invoiceItem['title'] : $invoiceItem->title;
		$itemType = InvoiceItemType::from($typeId);

		match ($itemType) {
			InvoiceItemType::CUSTOM => $item->custom = $itemTitle,
		};
	}

	/**
	 * Apply discount to the total price.
	 */
	private function applyDiscount(float $total, float $discount, string $discountType): float
	{
		if ($discountType === 'percentage') {
			return $total - ($total * $discount / 100);
		}

		return $total - $discount;
	}

	/**
	 * Prepare payment data for cash payment method.
	 */
	private function prepareCashPaymentData(array $attributes, Invoice $invoice, ?string $paymentReference): array
	{
		$paymentData = $invoice->payment_data;

		if ($paymentReference) {
			$paymentData['reference'] = $paymentReference;
		}

		if ($attributes['payment_date']) {
			$paymentData['transaction_id'] = $invoice->number;
			$paymentData['amount'] = $invoice->total_price;
		}

		$attributes['payment_data'] = $paymentData;

		if ($attributes['payment_date']) {
			$attributes['payment_date'] = $invoice->payment_date;
			$attributes['payment_status'] = InvoicePaymentStatus::PAID;
			$attributes['status'] = InvoiceStatus::COMPLETED;
		}

		return $attributes;
	}

	/**
	 * Prepare payment data for bank transfer payment method.
	 */
	private function prepareBankTransferPaymentData(array $attributes, Invoice $invoice, ?string $paymentReference): array
	{
		$paymentData = $invoice->payment_data;

		if ($paymentReference) {
			$paymentData['reference'] = $paymentReference;
		}

		if ($attributes['payment_date']) {
			$paymentData['transaction_id'] = $invoice->number;
			$paymentData['amount'] = $invoice->total_price;
		}

		$attributes['payment_data'] = $paymentData;

		if ($attributes['payment_date']) {
			$attributes['payment_date'] = $invoice->payment_date;
			$attributes['payment_status'] = InvoicePaymentStatus::PAID;
			$attributes['status'] = InvoiceStatus::COMPLETED;
		}

		return $attributes;
	}
}
