<?php

namespace App\Services;

use App\Enums\CustomerStatus;
use App\Enums\InvoiceStatus;
use App\Enums\RecurringInvoiceEndType;
use App\Enums\RecurringInvoiceFrequency;
use App\Enums\RecurringInvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
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
        private InvoiceService $invoiceService,
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

        $endDate = null;
        if ($endConditionType === RecurringInvoiceEndType::DATE && $recurringInvoice->end_date) {
            $endDate = Carbon::parse($recurringInvoice->end_date);
        } elseif ($endConditionType === RecurringInvoiceEndType::COUNT && $recurringInvoice->end_count) {
            $generatedCount = $recurringInvoice->invoices->count();
            $remainingCount = $recurringInvoice->end_count - $generatedCount;

            if ($remainingCount <= 0) {
                return $scheduledInvoices;
            }
        }

        $currentDate = $startDate->copy();
        $maxFutureDate = now()->addYear();
        $iteration = 0;
        $maxIterations = 100;
        $generatedCount = $recurringInvoice->invoices->count();

        while ($currentDate->lte($maxFutureDate) && $iteration < $maxIterations) {
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

            $currentDate = $this->addFrequency($currentDate, $frequency);
            $iteration++;
        }

        return $scheduledInvoices;
    }

    /**
     * Check if an invoice should be generated for the recurring invoice.
     */
    public function shouldGenerateInvoice(RecurringInvoice $recurringInvoice): bool
    {
        if ($recurringInvoice->status !== RecurringInvoiceStatus::ACTIVE) {
            return false;
        }

        if (! $recurringInvoice->next_run_date) {
            return false;
        }

        $nextRunDate = Carbon::parse($recurringInvoice->next_run_date);
        if (! $nextRunDate->isToday()) {
            return false;
        }

        $endConditionType = RecurringInvoiceEndType::from($recurringInvoice->end_condition_type);

        if ($endConditionType === RecurringInvoiceEndType::DATE && $recurringInvoice->end_date) {
            $endDate = Carbon::parse($recurringInvoice->end_date);
            if ($endDate->lt(today())) {
                return false;
            }
        }

        if ($endConditionType === RecurringInvoiceEndType::COUNT && $recurringInvoice->end_count) {
            $generatedCount = $recurringInvoice->invoices->count();
            if ($generatedCount >= $recurringInvoice->end_count) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the next run date for a recurring invoice.
     */
    public function calculateNextRunDate(RecurringInvoice $recurringInvoice, Carbon $currentDate): ?Carbon
    {
        $frequency = RecurringInvoiceFrequency::from($recurringInvoice->frequency);
        $endConditionType = RecurringInvoiceEndType::from($recurringInvoice->end_condition_type);

        $nextDate = $this->addFrequency($currentDate, $frequency);

        // Check if next date would exceed end_date
        if ($endConditionType === RecurringInvoiceEndType::DATE && $recurringInvoice->end_date) {
            $endDate = Carbon::parse($recurringInvoice->end_date);
            if ($nextDate->gt($endDate)) {
                return null;
            }
        }

        // Check if next generation would exceed end_count
        if ($endConditionType === RecurringInvoiceEndType::COUNT && $recurringInvoice->end_count) {
            $generatedCount = $recurringInvoice->invoices->count();
            // We're about to generate one more, so check if that would exceed the count
            if ($generatedCount + 1 >= $recurringInvoice->end_count) {
                return null;
            }
        }

        return $nextDate;
    }

    /**
     * Check and update end condition for a recurring invoice.
     */
    public function checkAndUpdateEndCondition(RecurringInvoice $recurringInvoice): bool
    {
        $endConditionType = RecurringInvoiceEndType::from($recurringInvoice->end_condition_type);
        $endReached = false;

        // Check if end date reached
        if ($endConditionType === RecurringInvoiceEndType::DATE && $recurringInvoice->end_date) {
            $endDate = Carbon::parse($recurringInvoice->end_date);
            if ($endDate->lte(today())) {
                $endReached = true;
            }
        }

        // Check if end count reached
        if ($endConditionType === RecurringInvoiceEndType::COUNT && $recurringInvoice->end_count) {
            $generatedCount = $recurringInvoice->invoices->count();
            if ($generatedCount >= $recurringInvoice->end_count) {
                $endReached = true;
            }
        }

        if ($endReached) {
            $this->recurringInvoiceRepository->update($recurringInvoice->id, [
                'status' => RecurringInvoiceStatus::ENDED,
            ]);
        }

        return $endReached;
    }

    /**
     * Generate an invoice from a recurring invoice.
     */
    public function generateInvoiceFromRecurring(
        RecurringInvoice $recurringInvoice,
        Carbon $invoiceDate,
        bool $sendAutomatically
    ): Invoice {
        $formattedItems = $recurringInvoice->getFormattedInvoiceItemsAttribute();

        $invoiceItems = $formattedItems->map(function ($item) {
            return [
                'type_id' => $item->type_id,
                'type' => $item->type,
                'title' => $item->title,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'amount' => $item->amount,
            ];
        })->toArray();

        $dueAfterDays = $recurringInvoice->due_after_days ?? 30;
        $dueDate = $invoiceDate->copy()->addDays($dueAfterDays);

        $attributes = [
            'status' => $sendAutomatically ? InvoiceStatus::ACTIVE : InvoiceStatus::DRAFT,
            'date' => $invoiceDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'customer_id' => $recurringInvoice->customer_id,
            'vendor_id' => $recurringInvoice->vendor_id,
            'recurring_invoice_id' => $recurringInvoice->id,
            'discount_type' => $recurringInvoice->discount_type,
            'discount_value' => $recurringInvoice->discount_value,
            'total_price' => $recurringInvoice->total_price,
            'notes' => $recurringInvoice->notes,
            'invoice_items' => $invoiceItems,
            'created_by' => $recurringInvoice->created_by,
            'notification' => false,
        ];

        $invoice = $this->invoiceService->createInvoice($attributes);

        return $invoice;
    }

    /**
     * Add frequency interval to a date.
     */
    private function addFrequency(Carbon $date, RecurringInvoiceFrequency $frequency): Carbon
    {
        return match ($frequency) {
            RecurringInvoiceFrequency::WEEKLY => $date->copy()->addWeek(),
            RecurringInvoiceFrequency::MONTHLY => $date->copy()->addMonth(),
            RecurringInvoiceFrequency::QUARTERLY => $date->copy()->addMonths(3),
            RecurringInvoiceFrequency::YEARLY => $date->copy()->addYear(),
        };
    }
}
