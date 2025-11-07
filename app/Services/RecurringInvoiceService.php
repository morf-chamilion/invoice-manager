<?php

namespace App\Services;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\RecurringInvoice;
use App\Models\Vendor;
use App\Repositories\RecurringInvoiceRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class RecurringInvoiceService extends BaseService
{
    public function __construct(
        private RecurringInvoiceRepository $recurringInvoiceRepository,
        private SettingService $settingService,
        private CustomerService $customerService,
    ) {
        parent::__construct($recurringInvoiceRepository);
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
        return $this->recurringInvoiceRepository->getAll();
    }

    /**
     * Create a new invoice.
     */
    public function createRecurringInvoice(array $attributes): RecurringInvoice
    {
        $notification = Arr::pull($attributes, 'notification');

        if ($this->getAdminAuthUser()) {
            $attributes['created_by'] = $this->getAdminAuthUser()->id;
            $attributes['vendor_id'] = $this->getAuthVendor()->id;
        }

        $recurringInvoice = $this->recurringInvoiceRepository->create($attributes);

        return $recurringInvoice;
    }

    /**
     * Get the specified invoice.
     */
    public function getRecurringInvoice(int $recurringInvoiceId): ?RecurringInvoice
    {
        return $this->recurringInvoiceRepository->getById($recurringInvoiceId);
    }

    /**
     * Get the specified invoice attribute.
     */
    public function getRecurringInvoiceWhere(string $columnName, mixed $value): ?RecurringInvoice
    {
        return $this->recurringInvoiceRepository->getFirstWhere($columnName, $value);
    }

    /**
     * Get the invoice that belongs to the customer.
     */
    public function getCustomerRecurringInvoice(int $recurringInvoiceId, int $customerId): ?RecurringInvoice
    {
        return $this->recurringInvoiceRepository->getCustomerRecurringInvoice($recurringInvoiceId, $customerId);
    }

    /**
     * Delete a specific recurring invoice.
     */
    public function deleteRecurringInvoice(int $recurringInvoiceId): int
    {
        return $this->recurringInvoiceRepository->delete($recurringInvoiceId);
    }

    /**
     * Update an existing invoice.
     */
    public function updateRecurringInvoice(int $recurringInvoiceId, array $newAttributes): bool
    {
        $notification = Arr::pull($newAttributes, 'notification');

        if ($this->getAdminAuthUser()) {
            $newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
        }

        $updated = $this->recurringInvoiceRepository->update($recurringInvoiceId, $newAttributes);

        return $updated;
    }

    /**
     * Calculate total amount.
     */
    public static function calculateRecurringInvoiceTotal(array|Collection $items, $discountValue, $discountType = 'fixed'): float
    {
        $itemsTotal = is_array($items)
            ? array_sum(array_column($items, 'amount'))
            : $items->sum('amount');

        $discountAmount = $discountType === 'percentage'
            ? ($itemsTotal * $discountValue) / 100
            : $discountValue;

        return max($itemsTotal - $discountAmount, 0);
    }

    public function getAllActiveRecurringInvoices(?Vendor $vendor = null, ?Customer $customer = null): Collection
    {
        return $this->recurringInvoiceRepository->getAllActive($vendor, $customer);
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
