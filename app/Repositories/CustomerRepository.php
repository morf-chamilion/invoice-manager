<?php

namespace App\Repositories;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Services\MediaService;
use App\Services\Traits\HandlesMedia;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class CustomerRepository extends BaseRepository
{
	use HandlesMedia;

	public function __construct(
		private Customer $customer,
		private MediaService $mediaService,
	) {
		parent::__construct($customer);
	}

	/**
	 * Get all customers.
	 */
	public function getAll(): Collection
	{
		return $this->customer::all();
	}

	/**
	 * Get all active customers.
	 */
	public function getAllActive(): Collection
	{
		return $this->customer
			->where('status', CustomerStatus::ACTIVE)
			->get();
	}

	/**
	 * Get the specified customer.
	 */
	public function getById(int $customerId): ?Customer
	{
		return $this->customer::find($customerId);
	}

	/**
	 * Get the customer by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?Customer
	{
		return $this->customer::where($columnName, $value)->first();
	}

	/**
	 * Delete a specific customer.
	 */
	public function delete(int $customerId): bool
	{
		$customer = $this->getById($customerId);

		$this->checkModelHasParentRelations($customer);

		try {
			return $customer->delete($customerId);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage());

			return false;
		}
	}

	/**
	 * Create a new customer.
	 */
	public function create(array $attributes): Customer
	{
		return $this->customer::create([
			...$attributes,
			'password' => Hash::make($attributes['password'])
		]);
	}

	/**
	 * Update an existing customer.
	 */
	public function update(int $customerId, array $newAttributes): bool
	{
		$password = Arr::pull($newAttributes, 'password');

		if ($password) {
			$newAttributes['password'] = Hash::make($password);
		}

		return $this->customer::whereId($customerId)
			->update($newAttributes);
	}
}
