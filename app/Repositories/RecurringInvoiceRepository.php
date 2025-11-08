<?php

namespace App\Repositories;

use App\Enums\RecurringInvoiceItemType;
use App\Enums\RecurringInvoiceStatus;
use App\Models\Customer;
use App\Models\RecurringInvoice;
use App\Models\RecurringInvoiceItem;
use App\Models\Vendor;
use App\Services\MediaService;
use App\Services\Traits\HandlesMedia;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class RecurringInvoiceRepository extends BaseRepository
{
    use HandlesMedia;

    public function __construct(
        private RecurringInvoice $recurringInvoice,
        private MediaService $mediaService,
    ) {
        parent::__construct($recurringInvoice);
    }

    /**
     * Get all invoices.
     */
    public function getAll(): Collection
    {
        return $this->recurringInvoice::all();
    }

    /**
     * Get all active customers.
     */
    public function getAllActive(?Vendor $vendor = null, ?Customer $customer = null): Collection
    {
        $query = $this->recurringInvoice->where('status', RecurringInvoiceStatus::ACTIVE);

        if ($vendor) {
            $query->where('vendor_id', $vendor->id);
        }

        if ($customer) {
            $query->where('customer_id', $customer->id);
        }

        return $query->get();
    }

    /**
     * Get the specified recurring invoice.
     */
    public function getById(int $recurringInvoiceId): ?RecurringInvoice
    {
        return $this->recurringInvoice::find($recurringInvoiceId);
    }

    /**
     * Get the recurring invoice by column name and value.
     */
    public function getFirstWhere(string $columnName, mixed $value): ?RecurringInvoice
    {
        return $this->recurringInvoice::where($columnName, $value)->first();
    }

    /**
     * Get recurring invoice that belongs to a customer.
     */
    public function getCustomerRecurringInvoice(int $recurringInvoiceId, int $customerId): ?RecurringInvoice
    {
        return $this->recurringInvoice->where('id', $recurringInvoiceId)
            ->where('customer_id', $customerId)
            ->where('status', '!=', RecurringInvoiceStatus::DRAFT)
            ->first();
    }

    /**
     * Get invoices that belongs to a customer.
     */
    public function getCustomerInvoices(int $customerId): ?Collection
    {
        return $this->recurringInvoice->where('customer_id', $customerId)
            ->where('status', '!=', RecurringInvoiceStatus::DRAFT)
            ->get();
    }

    /**
     * Delete a specific invoice.
     */
    public function delete(int $recurringInvoiceId): bool|QueryException
    {
        $recurringInvoice = $this->getById($recurringInvoiceId);

        $this->checkModelHasParentRelations($recurringInvoice);

        try {
            return $recurringInvoice->delete($recurringInvoiceId);
        } catch (QueryException $e) {
            throw new \Exception($e->getMessage());

            return false;
        }
    }

    /**
     * Create a new invoice.
     */
    public function create(array $attributes): RecurringInvoice
    {
        $invoiceItems = Arr::pull($attributes, 'invoice_items');

        // Set defaults for required fields that might be missing
        if (empty($attributes['start_date'])) {
            $attributes['start_date'] = now()->toDateString();
        }

        // Create new instance and fill with attributes
        $recurringInvoice = $this->recurringInvoice->newInstance();
        $recurringInvoice->fill($attributes);

        // Set vendor association
        if (! empty($attributes['vendor_id'])) {
            $recurringInvoice->vendor()->associate($attributes['vendor_id']);
        }

        // Set defaults for nullable fields if not provided
        $recurringInvoice->discount_type = $attributes['discount_type'] ?? null;
        $recurringInvoice->discount_value = $attributes['discount_value'] ?? null;

        // Save to get the ID
        $recurringInvoice->save();

        // Set number after we have the ID
        $recurringInvoice->number = (string) $recurringInvoice->id;

        // Sync invoice items and calculate total price
        $totalPrice = $attributes['total_price'] ?? 0;
        if ($invoiceItems) {
            $recurringInvoice->invoiceItems()->delete();
            $totalPrice = $this->syncInvoiceItems($recurringInvoice, $invoiceItems);
        }

        // Update total price and save again
        $recurringInvoice->total_price = $totalPrice;
        $recurringInvoice->save();

        return $recurringInvoice;
    }

    /**
     * Update an existing recurring invoice.
     */
    public function update(int $recurringInvoiceId, array $newAttributes): bool
    {
        $invoiceItems = Arr::pull($newAttributes, 'invoice_items');

        $recurringInvoice = $this->recurringInvoice::findOrFail($recurringInvoiceId);

        $updated = $recurringInvoice->update($newAttributes);

        $totalPrice = 0;

        if ($invoiceItems) {
            $totalPrice = $this->syncInvoiceItems($recurringInvoice, $invoiceItems);
            $recurringInvoice->total_price = $totalPrice;

            $recurringInvoice->save();
        } else {
            $recurringInvoice->recurringInvoiceItems()->delete();
        }

        return $updated;
    }

    /**
     * Update invoice items and calculate total price.
     */
    private function syncInvoiceItems($recurringInvoice, array|object $invoiceItems): float
    {
        $recurringInvoice->invoiceItems()->delete();

        $totalPrice = 0;

        foreach ($invoiceItems as $invoiceItem) {
            $item = new RecurringInvoiceItem;
            $item->recurring_invoice_id = $recurringInvoice->id;

            $this->setInvoiceItemType($item, $invoiceItem);

            $item->description = is_array($invoiceItem) ? $invoiceItem['description'] : $invoiceItem->description;
            $item->quantity = is_array($invoiceItem) ? $invoiceItem['quantity'] : $invoiceItem->quantity;
            $item->unit_price = is_array($invoiceItem) ? $invoiceItem['unit_price'] : $invoiceItem->unit_price;
            $item->amount = is_array($invoiceItem) ? $invoiceItem['amount'] : $invoiceItem->amount;

            $item->save();

            $totalPrice += $item->amount;
        }

        return $this->applyDiscount($totalPrice, $recurringInvoice->discount_value ?? 0, $recurringInvoice->discount_type ?? 0);
    }

    /**
     * Set the item type for an recurring invoice item.
     */
    private function setInvoiceItemType(RecurringInvoiceItem $item, array|object $invoiceItem)
    {
        $typeId = is_array($invoiceItem) ? $invoiceItem['type_id'] : $invoiceItem->type_id;
        $itemTitle = is_array($invoiceItem) ? $invoiceItem['title'] : $invoiceItem->title;
        $itemType = RecurringInvoiceItemType::from($typeId);

        match ($itemType) {
            RecurringInvoiceItemType::CUSTOM => $item->custom = $itemTitle,
        };
    }

    /**
     * Apply discount to the total price.
     */
    private function applyDiscount(float $total, float $discount, string $discountType): float
    {
        if ($discountType === 'percentage') {
            return $total - ($total * $discount / 100);
        }

        return $total - $discount;
    }
}
