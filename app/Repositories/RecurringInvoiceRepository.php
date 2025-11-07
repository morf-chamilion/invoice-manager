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
     * Get the last invoice for the vendor.
     */
    public function getLastVendorRecurringInvoice(int $vendorId): ?RecurringInvoice
    {
        return $this->recurringInvoice->where('vendor_id', $vendorId)
            ->orderBy('vendor_recurring_invoice_number', 'desc')
            ->first();
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
        $recurringInvoiceItems = Arr::pull($attributes, 'recurring_invoice_items');

        $recurringInvoice = $this->recurringInvoice::create($attributes);
        $recurringInvoice->vendor()->associate($attributes['vendor_id']);

        $lastRecurringInvoice = $this->getLastVendorRecurringInvoice($attributes['vendor_id']);
        $recurringInvoice->vendor_recurring_invoice_number = $lastRecurringInvoice ? ++$lastRecurringInvoice->vendor_recurring_invoice_number : 1;
        $recurringInvoice->number = $recurringInvoice->id;
        $recurringInvoice->discount_type = $attributes['discount_type'];
        $recurringInvoice->discount_value = $attributes['discount_value'];

        $totalPrice = 0;

        if ($recurringInvoiceItems) {
            $recurringInvoice->recurringInvoiceItems()->delete();

            $totalPrice = $this->syncRecurringInvoiceItems($recurringInvoice, $recurringInvoiceItems);
            $recurringInvoice->total_price = $totalPrice;
        }

        $recurringInvoice->total_price = $totalPrice;

        $recurringInvoice->save();

        return $recurringInvoice;
    }

    /**
     * Update an existing recurring invoice.
     */
    public function update(int $recurringInvoiceId, array $newAttributes): bool
    {
        $recurringInvoiceItems = Arr::pull($newAttributes, 'recurring_invoice_items');

        $recurringInvoice = $this->recurringInvoice::findOrFail($recurringInvoiceId);

        $updated = $recurringInvoice->update($newAttributes);

        $totalPrice = 0;

        if ($recurringInvoiceItems) {
            $totalPrice = $this->syncRecurringInvoiceItems($recurringInvoice, $recurringInvoiceItems);
            $recurringInvoice->total_price = $totalPrice;

            $recurringInvoice->save();
        } else {
            $recurringInvoice->recurringInvoiceItems()->delete();
        }

        return $updated;
    }

    /**
     * Update recurring invoice items and calculate total price.
     */
    private function syncRecurringInvoiceItems($recurringInvoice, array|object $recurringInvoiceItems): float
    {
        $recurringInvoice->recurringInvoiceItems()->delete();

        $totalPrice = 0;

        foreach ($recurringInvoiceItems as $recurringInvoiceItem) {
            $item = new RecurringInvoiceItem;
            $item->recurring_invoice_id = $recurringInvoice->id;

            $this->setRecurringInvoiceItemType($item, $recurringInvoiceItem);

            $item->description = is_array($recurringInvoiceItem) ? $recurringInvoiceItem['description'] : $recurringInvoiceItem->description;
            $item->quantity = is_array($recurringInvoiceItem) ? $recurringInvoiceItem['quantity'] : $recurringInvoiceItem->quantity;
            $item->unit_price = is_array($recurringInvoiceItem) ? $recurringInvoiceItem['unit_price'] : $recurringInvoiceItem->unit_price;
            $item->amount = is_array($recurringInvoiceItem) ? $recurringInvoiceItem['amount'] : $recurringInvoiceItem->amount;

            $item->save();

            $totalPrice += $item->amount;
        }

        return $this->applyDiscount($totalPrice, $recurringInvoice->discount_value ?? 0, $recurringInvoice->discount_type ?? 0);
    }

    /**
     * Set the item type for an recurring invoice item.
     */
    private function setRecurringInvoiceItemType(RecurringInvoiceItem $item, array|object $recurringInvoiceItem)
    {
        $typeId = is_array($recurringInvoiceItem) ? $recurringInvoiceItem['type_id'] : $recurringInvoiceItem->type_id;
        $itemTitle = is_array($recurringInvoiceItem) ? $recurringInvoiceItem['title'] : $recurringInvoiceItem->title;
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
