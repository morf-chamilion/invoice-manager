<?php

namespace App\Services;

use App\Models\Vendor;
use App\Repositories\VendorRepository;
use Illuminate\Support\Collection;

class VendorService extends BaseService
{
	public function __construct(
		private VendorRepository $invoiceRepository,
		private SettingService $settingService,
	) {
		parent::__construct($invoiceRepository);
	}

	/**
	 * Get all invoices.
	 */
	public function getAllVendors(): Collection
	{
		return $this->invoiceRepository->getAll();
	}

	/**
	 * Create a new invoice.
	 */
	public function createVendor(array $attributes): Vendor
	{
		if ($this->getAdminAuthUser()) {
			$attributes['created_by'] = $this->getAdminAuthUser()->id;
		}

		$invoice = $this->invoiceRepository->create($attributes);

		return $invoice;
	}

	/**
	 * Get the specified invoice.
	 */
	public function getVendor(int $invoiceId): ?Vendor
	{
		return $this->invoiceRepository->getById($invoiceId);
	}

	/**
	 * Get the specified invoice attribute.
	 */
	public function getVendorWhere(string $columnName, mixed $value): ?Vendor
	{
		return $this->invoiceRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Delete a specific invoice.
	 */
	public function deleteVendor(int $invoiceId): int
	{
		return $this->invoiceRepository->delete($invoiceId);
	}

	/**
	 * Update an existing invoice.
	 */
	public function updateVendor(int $invoiceId, array $newAttributes): bool
	{
		if ($this->getAdminAuthUser()) {
			$newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
		}

		$updated = $this->invoiceRepository->update($invoiceId, $newAttributes);

		return $updated;
	}
}
