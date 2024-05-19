<?php

namespace App\Services;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Notifications\Customer\CustomerCreateNotification;
use App\Providers\AuthServiceProvider;
use App\Repositories\CustomerRepository;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Exception;

class CustomerService extends BaseService
{
	public function __construct(
		private CustomerRepository $customerRepository,
		private SettingService $settingService,
	) {
		parent::__construct($customerRepository);
	}

	/**
	 * Get all customers.
	 */
	public function getAllCustomers(): Collection
	{
		return $this->customerRepository->getAll();
	}

	/**
	 * Get all active customers.
	 */
	public function getAllActiveCustomers(): Collection
	{
		return $this->customerRepository->getAllActive();
	}

	/**
	 * Create a new customer.
	 */
	public function createCustomer(array $attributes): Customer
	{
		$notification = Arr::pull($attributes, 'notification');

		if ($this->getAdminAuthUser()) {
			$attributes['created_by'] = $this->getAdminAuthUser()->id;
		} else {
			$attributes['created_by'] = AuthServiceProvider::SUPER_ADMIN;
		}

		$customer = $this->customerRepository->create($attributes);

		$this->markEmailVerified($customer, $attributes['status']);

		if ($notification) {
			$this->customerCreateNotification($customer, $attributes['password']);
		}

		return $customer;
	}

	/**
	 * Get the specified customer.
	 */
	public function getCustomer(int $customerId): ?Customer
	{
		return $this->customerRepository->getById($customerId);
	}

	/**
	 * Get the specified customer attribute.
	 */
	public function getCustomerWhere(string $columnName, mixed $value): ?Customer
	{
		return $this->customerRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Delete a specific customer.
	 */
	public function deleteCustomer(int $customerId): int
	{
		return $this->customerRepository->delete($customerId);
	}

	/**
	 * Update an existing customer.
	 */
	public function updateCustomer(int $customerId, array $newAttributes): bool
	{
		$status = isset($newAttributes['status']) ? $newAttributes['status'] : null;

		if ($this->getAdminAuthUser()) {
			$newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
		} else if ($this->getCustomerAuthUser()) {
			$newAttributes['updated_by'] = $this->getCustomerAuthUser()->id;
		}

		$updated = $this->customerRepository->update($customerId, $newAttributes);

		$customer = $this->getCustomer($customerId);

		$this->markEmailVerified($customer, $status);

		return $updated;
	}

	/**
	 * Customer routes.
	 */
	public function getRoutes(): array
	{
		return [
			// CustomerRoutePath::DASHBOARD_SHOW => 'Dashboard',
			CustomerRoutePath::INVOICE_INDEX => 'Invoices',
			CustomerRoutePath::EDIT => 'Profile Settings',
		];
	}

	/**
	 * Mark the given customer's email as verified.
	 */
	private function markEmailVerified(Customer $customer, $status): bool
	{
		if ($this->getAdminAuthUser() && $status == CustomerStatus::ACTIVE->value) {
			$customer->markEmailAsVerified();
		}

		return false;
	}

	/**
	 * Handle customer create notfications.
	 */
	private function customerCreateNotification(
		Customer $customer,
		#[\SensitiveParameter]
		string $customerPasswordPlainText,
	): bool|Exception {
		try {
			$customer->notify(
				new CustomerCreateNotification($customer, $customerPasswordPlainText),
			);

			return true;
		} catch (Exception $e) {
			return $e;
		}

		return false;
	}
}
