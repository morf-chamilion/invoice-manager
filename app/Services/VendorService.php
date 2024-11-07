<?php

namespace App\Services;

use App\Models\Vendor;
use App\Repositories\VendorRepository;
use Illuminate\Support\Collection;

class VendorService extends BaseService
{
	public function __construct(
		private VendorRepository $vendorRepository,
		private SettingService $settingService,
	) {
		parent::__construct($vendorRepository);
	}

	/**
	 * Get all vendors.
	 */
	public function getAllVendors(): Collection
	{
		return $this->vendorRepository->getAll();
	}

	/**
	 * Create a new vendor.
	 */
	public function createVendor(array $attributes): Vendor
	{
		if ($this->getAdminAuthUser()) {
			$attributes['created_by'] = $this->getAdminAuthUser()->id;
		}

		$vendor = $this->vendorRepository->create($attributes);

		return $vendor;
	}

	/**
	 * Get the specified vendor.
	 */
	public function getVendor(int $vendorId): ?Vendor
	{
		return $this->vendorRepository->getById($vendorId);
	}

	/**
	 * Get the specified vendor attribute.
	 */
	public function getVendorWhere(string $columnName, mixed $value): ?Vendor
	{
		return $this->vendorRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Delete a specific vendor.
	 */
	public function deleteVendor(int $vendorId): int
	{
		return $this->vendorRepository->delete($vendorId);
	}

	/**
	 * Update an existing vendor.
	 */
	public function updateVendor(int $vendorId, array $newAttributes): bool
	{
		if ($this->getAdminAuthUser()) {
			$newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
		}

		$updated = $this->vendorRepository->update($vendorId, $newAttributes);

		return $updated;
	}

	/**
	 * Update an existing vendor.
	 */
	public function updateVendorSettings(int $vendorId, array $newAttributes, ?string $action = null): bool
	{
		if ($this->getAdminAuthUser()) {
			$newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
		}

		$updated = $this->vendorRepository->updateSettings($vendorId, $newAttributes, $action);

		return $updated;
	}
}
