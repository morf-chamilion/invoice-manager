<?php

namespace App\Repositories;

use App\Models\Vendor;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class VendorRepository extends BaseRepository
{
	public function __construct(
		private Vendor $vendor,
	) {
		parent::__construct($vendor);
	}

	/**
	 * Get all vendors.
	 */
	public function getAll(): Collection
	{
		return $this->vendor::all();
	}

	/**
	 * Get the specified vendor.
	 */
	public function getById(int $vendorId): ?Vendor
	{
		return $this->vendor::find($vendorId);
	}

	/**
	 * Get the vendor by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?Vendor
	{
		return $this->vendor::where($columnName, $value)->first();
	}

	/**
	 * Delete a specific vendor.
	 */
	public function delete(int $vendorId): bool|QueryException
	{
		$vendor = $this->getById($vendorId);

		$this->checkModelHasParentRelations($vendor);

		try {
			return $vendor->delete($vendorId);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage());

			return false;
		}
	}

	/**
	 * Create a new vendor.
	 */
	public function create(array $attributes): Vendor
	{
		return $this->vendor::create($attributes);
	}

	/**
	 * Update an existing vendor.
	 */
	public function update(int $vendorId, array $newAttributes): bool
	{
		return $this->vendor::whereId($vendorId)
			->update($newAttributes);
	}
}
