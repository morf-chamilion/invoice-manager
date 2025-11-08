<?php

namespace App\Services;

use App\Enums\CustomerStatus;
use App\Enums\RecurringInvoiceEndType;
use App\Enums\RecurringInvoiceFrequency;
use App\Enums\RecurringInvoiceStatus;
use App\Models\Customer;
use App\Models\RecurringInvoice;
use App\Models\Vendor;
use App\Repositories\RecurringInvoiceRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
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

    /**
     * Calculate scheduled invoices based on recurring invoice settings.
     */
    public function calculateScheduledInvoices(RecurringInvoice $recurringInvoice): Collection
    {
        $scheduledInvoices = collect();

        if ($recurringInvoice->status !== RecurringInvoiceStatus::ACTIVE) {
            return $scheduledInvoices;
        }

        $frequency = RecurringInvoiceFrequency::from($recurringInvoice->frequency);
        $endConditionType = RecurringInvoiceEndType::from($recurringInvoice->end_condition_type);
        $dueAfterDays = $recurringInvoice->due_after_days ?? 30;

        // Determine starting point - use next_run_date if available, otherwise calculate from start_date
        $startDate = $recurringInvoice->next_run_date
            ? Carbon::parse($recurringInvoice->next_run_date)
            : Carbon::parse($recurringInvoice->start_date);

        // If start date is in the past, calculate next occurrence from now
        if ($startDate->isPast()) {
            $startDate = Carbon::parse($recurringInvoice->start_date);
            while ($startDate->isPast()) {
                $startDate = $this->addFrequency($startDate, $frequency);
            }
        }

        // Get existing invoice dates to avoid duplicates
        $existingInvoiceDates = $recurringInvoice->invoices->map(function ($invoice) {
            return Carbon::parse($invoice->date)->format('Y-m-d');
        })->toArray();

        // Determine end date
        $endDate = null;
        if ($endConditionType === RecurringInvoiceEndType::DATE && $recurringInvoice->end_date) {
            $endDate = Carbon::parse($recurringInvoice->end_date);
        } elseif ($endConditionType === RecurringInvoiceEndType::COUNT && $recurringInvoice->end_count) {
            // Calculate how many invoices have been generated
            $generatedCount = $recurringInvoice->invoices->count();
            $remainingCount = $recurringInvoice->end_count - $generatedCount;

            if ($remainingCount <= 0) {
                return $scheduledInvoices;
            }
        }

        // Generate scheduled invoices (up to 1 year in advance or until end condition)
        $currentDate = $startDate->copy();
        $maxFutureDate = now()->addYear(); // Show up to 1 year in advance
        $iteration = 0;
        $maxIterations = 100; // Safety limit
        $generatedCount = $recurringInvoice->invoices->count();

        while ($currentDate->lte($maxFutureDate) && $iteration < $maxIterations) {
            // Check if we've reached the end condition
            if ($endDate && $currentDate->gt($endDate)) {
                break;
            }

            // Check count limit
            if ($endConditionType === RecurringInvoiceEndType::COUNT && $recurringInvoice->end_count) {
                if ($generatedCount + $iteration >= $recurringInvoice->end_count) {
                    break;
                }
            }

            // Skip if invoice already exists for this date
            $dateKey = $currentDate->format('Y-m-d');
            if (! in_array($dateKey, $existingInvoiceDates)) {
                $scheduledInvoices->push((object) [
                    'date' => $currentDate->copy(),
                    'due_date' => $currentDate->copy()->addDays($dueAfterDays),
                    'amount' => $recurringInvoice->total_price,
                ]);
            }

            // Move to next date based on frequency
            $currentDate = $this->addFrequency($currentDate, $frequency);
            $iteration++;
        }

        return $scheduledInvoices;
    }

    /**
     * Add frequency interval to a date.
     */
    private function addFrequency(Carbon $date, RecurringInvoiceFrequency $frequency): Carbon
    {
        return match ($frequency) {
            RecurringInvoiceFrequency::WEEKLY => $date->copy()->addWeek(),
            RecurringInvoiceFrequency::BIWEEKLY => $date->copy()->addWeeks(2),
            RecurringInvoiceFrequency::MONTHLY => $date->copy()->addMonth(),
            RecurringInvoiceFrequency::QUARTERLY => $date->copy()->addMonths(3),
            RecurringInvoiceFrequency::YEARLY => $date->copy()->addYear(),
        };
    }
}
