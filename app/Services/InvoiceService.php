<?php

namespace App\Services;

use App\Enums\CustomerStatus;
use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Vendor;
use App\Notifications\Invoice\InvoiceCreateCustomerNotification;
use App\Notifications\Invoice\InvoiceOverdueCustomerNotification;
use App\Notifications\Invoice\InvoiceUpdateCustomerNotification;
use App\Repositories\InvoiceRepository;
use App\RoutePaths\Pdf\PdfRoutePath;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;
use Exception;

class InvoiceService extends BaseService
{
	public function __construct(
		private InvoiceRepository $invoiceRepository,
		private SettingService $settingService,
		private CustomerService $customerService,
	) {
		parent::__construct($invoiceRepository);
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
	 * Get all invoices.
	 */
	public function getAllInvoices(): Collection
	{
		return $this->invoiceRepository->getAll();
	}

	/**
	 * Create a new invoice.
	 */
	public function createInvoice(array $attributes): Invoice
	{
		$notification = Arr::pull($attributes, 'notification');

		if ($this->getAdminAuthUser()) {
			$attributes['created_by'] = $this->getAdminAuthUser()->id;
			$attributes['vendor_id'] = $this->getAuthVendor()->id;
		}

		$invoice = $this->invoiceRepository->create($attributes);

		if ($notification) {
			$this->invoiceCreateMailNotification($invoice);
		}

		return $invoice;
	}

	/**
	 * Get the specified invoice.
	 */
	public function getInvoice(int $invoiceId): ?Invoice
	{
		return $this->invoiceRepository->getById($invoiceId);
	}

	/**
	 * Get the specified invoice attribute.
	 */
	public function getInvoiceWhere(string $columnName, mixed $value): ?Invoice
	{
		return $this->invoiceRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Get the invoice that belongs to the customer.
	 */
	public function getCustomerInvoice(int $invoiceId, int $customerId): ?Invoice
	{
		return $this->invoiceRepository->getCustomerInvoice($invoiceId, $customerId);
	}

	/**
	 * Get the invoices that belongs to the customer.
	 */
	public function getCustomerInvoices(int $customerId): Collection
	{
		return $this->invoiceRepository->getCustomerInvoices($customerId);
	}

	/**
	 * Get the count of invoices that are past the due date.
	 */
	public function dueInvoiceCount(): int
	{
		return $this->invoiceRepository->getModel()
			->whereDate('due_date', '<', now())
			->where('status', '!=', InvoiceStatus::DRAFT)
			->where('payment_status', '!=', InvoicePaymentStatus::PAID)
			->count();
	}

	/**
	 * Check if the invoice is currently due.
	 */
	public function isDueInvoice(Invoice $invoice): bool
	{
		return $this->invoiceRepository->getModel()
			->where('id', $invoice->id)
			->whereDate('due_date', '<', now())
			->where('status', '!=', InvoiceStatus::DRAFT)
			->where('payment_status', '!=', InvoicePaymentStatus::PAID)
			->exists();
	}

	/**
	 * Delete a specific invoice.
	 */
	public function deleteInvoice(int $invoiceId): int
	{
		return $this->invoiceRepository->delete($invoiceId);
	}

	/**
	 * Update an existing invoice.
	 */
	public function updateInvoice(int $invoiceId, array $newAttributes): bool
	{
		$notification = Arr::pull($newAttributes, 'notification');

		if ($this->getAdminAuthUser()) {
			$newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
		}

		$updated = $this->invoiceRepository->update($invoiceId, $newAttributes);

		if ($notification) {
			$this->invoiceUpdateMailNotification(
				$this->getInvoice($invoiceId)
			);
		}

		return $updated;
	}

	/**
	 * Calculate total amount.
	 */
	public static function calculateTotal(array|Collection $items, $discountValue, $discountType = 'fixed'): float
	{
		$itemsTotal = is_array($items)
			? array_sum(array_column($items, 'amount'))
			: $items->sum('amount');

		$discountAmount = $discountType === 'percentage'
			? ($itemsTotal * $discountValue) / 100
			: $discountValue;

		return max($itemsTotal - $discountAmount, 0);
	}

	/**
	 * Get invoice file name.
	 */
	public function invoiceFileName(Invoice $invoice): string
	{
		return Str::of($invoice->number)
			->prepend('-')
			->prepend('INVOICE')
			->replace('/', '-');
	}

	/**
	 * Generate invoice PDF.
	 */
	public function invoicePDF(Invoice $invoice): DomPDF
	{
		return Pdf::loadView(PdfRoutePath::INVOICE, [
			'invoice' => $invoice,
		]);
	}

	/**
	 * Handle invoice create mail notfications.
	 */
	private function invoiceCreateMailNotification(Invoice $invoice): bool|Exception
	{
		try {
			$invoice->notify(new InvoiceCreateCustomerNotification($invoice));

			return true;
		} catch (Exception $e) {
			return $e;
		}

		return false;
	}

	/**
	 * Handle invoice update mail notfications.
	 */
	private function invoiceUpdateMailNotification(Invoice $invoice): bool|Exception
	{
		try {
			$invoice->notify(new InvoiceUpdateCustomerNotification($invoice));

			return true;
		} catch (Exception $e) {
			return $e;
		}

		return false;
	}

	/**
	 * Handle invoice overdue mail notfications.
	 */
	public function invoiceOverdueMailNotification(Invoice $invoice): bool|Exception
	{
		try {
			$invoice->notify(new InvoiceOverdueCustomerNotification($invoice));

			return true;
		} catch (Exception $e) {
			return $e;
		}

		return false;
	}

	/**
	 * return the custom filtered result as a page.
	 *
	 * @return integer 	$data[count]		Results Count
	 * @return array 	$data[data]			Results
	 */
	public function getAllWithCustomFilter($filterQuery, $filterColumns)
	{
		$query = $this->invoiceRepository->getModel()->select('*');

		$query->where(function ($subQuery) use ($filterQuery) {
			if ($this->getAdminAuthUser()->vendor) {
				$subQuery->whereHas('vendor', function ($subQuery) {
					$subQuery->where('id', $this->getAuthVendor()->id);
				});
			}

			if (isset($filterQuery->status)) {
				if ($filterQuery->status === 'past_due_date') {
					$subQuery->where('status', InvoiceStatus::ACTIVE->value);
					$subQuery->where('payment_status', '!=', InvoicePaymentStatus::PAID->value);
					$subQuery->whereDate('due_date', '<', now());
				} else {
					$subQuery->where('status', $filterQuery->status);
				}
			}

			if (isset($filterQuery->payment_status)) {
				$subQuery->where('payment_status', $filterQuery->payment_status);
			}

			if (isset($filterQuery->number)) {
				$subQuery->where('number', 'like', '%' . $filterQuery->number . '%');
			}

			if (isset($filterQuery->date_start) && isset($filterQuery->date_end)) {
				$subQuery->whereBetween('created_at', [$filterQuery->date_start . ' 00:00:00', $filterQuery->date_end . ' 23:59:59']);
			}

			if (isset($filterQuery->customer)) {
				$subQuery->whereHas('customer', function ($subQuery) use ($filterQuery) {
					$subQuery->where('id', $filterQuery->customer);
				});
			}

			if (isset($filterQuery->company)) {
				$subQuery->whereHas('customer', function ($subQuery) use ($filterQuery) {
					$subQuery->where('company', 'like', '%' . $filterQuery->company . '%');
				});
			}
		});

		$query->where(function ($query) use ($filterColumns, $filterQuery) {
			foreach ($filterColumns as $column) {
				if ($column) {
					$query->orWhere($column, 'like', '%' . $filterQuery->search['value'] . '%');
				}
			}
		});

		$data['count'] = $query->count();
		$data['data'] = [];

		$orderByColumn = $filterColumns[$filterQuery->order[0]['column']] ?? $filterColumns[count($filterColumns) - 1];
		$orderByDirection = $filterQuery->order[0]['dir'];

		$query->orderBy($orderByColumn, $orderByDirection);

		if ($filterQuery->length != -1) {
			$query->skip($filterQuery->start)->take($filterQuery->length);
		}

		if ($this->getAdminAuthUser()->vendor) {
			$data['data'] = $query->get();
		}

		return $data;
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
	 * Store a new customer.
	 */
	public function storeCustomer(array $attributes): Customer
	{
		$customer = $this->customerService->repository
			->getModel()::where('email', $attributes['email'])
			->where('vendor_id', $this->getAdminAuthUser()->vendor->id)
			->first();

		if ($customer) {
			return $customer;
		}

		return $this->customerService->createCustomer([
			...$attributes,
			'status' => CustomerStatus::ACTIVE,
		]);
	}
}
