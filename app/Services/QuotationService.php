<?php

namespace App\Services;

use App\Enums\CustomerStatus;
use App\Enums\QuotationStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Vendor;
use App\Notifications\Quotation\QuotationCreateCustomerNotification;
use App\Notifications\Quotation\QuotationUpdateCustomerNotification;
use App\Repositories\QuotationRepository;
use App\RoutePaths\Pdf\PdfRoutePath;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;
use Exception;
use Illuminate\Support\Facades\Date;

class QuotationService extends BaseService
{
	public function __construct(
		private QuotationRepository $quotationRepository,
		private SettingService $settingService,
		private CustomerService $customerService,
		private InvoiceService $invoiceService,
	) {
		parent::__construct($quotationRepository);
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
	 * Get all quotations.
	 */
	public function getAllQuotations(): Collection
	{
		return $this->quotationRepository->getAll();
	}

	/**
	 * Create a new quotation.
	 */
	public function createQuotation(array $attributes): Quotation
	{
		$notification = Arr::pull($attributes, 'notification');

		if ($this->getAdminAuthUser()) {
			$attributes['created_by'] = $this->getAdminAuthUser()->id;
			$attributes['vendor_id'] = $this->getAuthVendor()->id;
		}

		$quotation = $this->quotationRepository->create($attributes);

		if ($notification) {
			$this->quotationCreateMailNotification($quotation);
		}

		return $quotation;
	}

	/**
	 * Get the specified quotation.
	 */
	public function getQuotation(int $quotationId): ?Quotation
	{
		return $this->quotationRepository->getById($quotationId);
	}

	/**
	 * Get the specified quotation attribute.
	 */
	public function getQuotationWhere(string $columnName, mixed $value): ?Quotation
	{
		return $this->quotationRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Get the quotation that belongs to the customer.
	 */
	public function getCustomerQuotation(int $quotationId, int $customerId): ?Quotation
	{
		return $this->quotationRepository->getCustomerQuotation($quotationId, $customerId);
	}

	/**
	 * Get the quotations that belongs to the customer.
	 */
	public function getCustomerQuotations(int $customerId): Collection
	{
		return $this->quotationRepository->getCustomerQuotations($customerId);
	}

	/**
	 * Delete a specific quotation.
	 */
	public function deleteQuotation(int $quotationId): int
	{
		return $this->quotationRepository->delete($quotationId);
	}

	/**
	 * Update an existing quotation.
	 */
	public function updateQuotation(int $quotationId, array $newAttributes): bool
	{
		$notification = Arr::pull($newAttributes, 'notification');

		if ($this->getAdminAuthUser()) {
			$newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
		}

		$updated = $this->quotationRepository->update($quotationId, $newAttributes);

		if ($notification) {
			$this->quotationUpdateMailNotification(
				$this->getQuotation($quotationId)
			);
		}

		return $updated;
	}

	/**
	 * Generate an invoice for this quotation.
	 */
	public function generateInvoice(Quotation $quotation): ?Invoice
	{
		$quotationData = $quotation->toArray();
		$quotationData['invoice_items'] = $quotation->formattedQuotationItems->toArray();

		$attributes = Arr::except($quotationData, [
			'vendor_quotation_number',
			'valid_until_date',
			'created_at',
			'created_by',
			'updated_at',
			'updated_by',
			'vendor_id',
			'invoice_id',
			'status',
			'id',
		]);

		$invoice = $this->invoiceService->createInvoice([
			'due_date' => Date::today()->addWeek(),
			...$attributes
		]);

		if ($invoice) {
			$this->updateQuotation($quotation->id, [
				'status' => QuotationStatus::CONVERTED,
				'invoice_id' => $invoice->id,
			]);
		}

		return $invoice;
	}

	/**
	 * Check if the quotation is past valid date.
	 */
	public function isPastValidUntilQuotation(Quotation $quotation): bool
	{
		return $this->quotationRepository->getModel()
			->where('id', $quotation->id)
			->whereDate('valid_until_date', '<', now())
			->where('status', '!=', QuotationStatus::DRAFT)
			->where('invoice_id', '=', null)
			->exists();
	}

	/**
	 * Get quotation file name.
	 */
	public function quotationFileName(Quotation $quotation): string
	{
		return Str::of($quotation->number)
			->prepend('-')
			->prepend('QUOTATION')
			->replace('/', '-');
	}

	/**
	 * Generate quotation PDF.
	 */
	public function quotationPDF(Quotation $quotation): DomPDF
	{
		return Pdf::loadView(PdfRoutePath::QUOTATION, [
			'quotation' => $quotation,
		]);
	}

	/**
	 * Handle quotation create mail notfications.
	 */
	private function quotationCreateMailNotification(Quotation $quotation): bool|Exception
	{
		try {
			$quotation->notify(new QuotationCreateCustomerNotification($quotation));

			return true;
		} catch (Exception $e) {
			return $e;
		}

		return false;
	}

	/**
	 * Handle quotation update mail notfications.
	 */
	private function quotationUpdateMailNotification(Quotation $quotation): bool|Exception
	{
		try {
			$quotation->notify(new QuotationUpdateCustomerNotification($quotation));

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
		$query = $this->quotationRepository->getModel()->select('*');

		$query->where(function ($subQuery) use ($filterQuery) {
			if ($this->getAdminAuthUser()->vendor) {
				$subQuery->whereHas('vendor', function ($subQuery) {
					$subQuery->where('id', $this->getAuthVendor()->id);
				});
			}

			if (isset($filterQuery->status)) {
				$subQuery->where('status', $filterQuery->status);
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

		$orderByColumn = $filterColumns[$filterQuery->order[0]['column']] ?? $filterColumns[count($filterColumns) - 1];
		$orderByDirection = $filterQuery->order[0]['dir'];

		$query->orderBy($orderByColumn, $orderByDirection);

		if ($filterQuery->length != -1) {
			$query->skip($filterQuery->start)->take($filterQuery->length);
		}

		$data['data'] = $query->get();

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
		return $this->customerService->createCustomer([
			...$attributes,
			'password' => Str::password(30),
			'status' => CustomerStatus::ACTIVE,
		]);
	}
}
