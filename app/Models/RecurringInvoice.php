<?php

namespace App\Models;

use App\Enums\InvoiceItemType;
use App\Enums\PaymentStatus;
use App\Enums\RecurringInvoiceStatus;
use App\Helpers\MoneyHelper;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Traits\HasCreatedBy;
use App\Models\Traits\HasUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RecurringInvoice extends Model implements HasMedia, HasRelationsInterface
{
    use HasCreatedBy, HasFactory, HasUpdatedBy, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'start_date',
        'due_after_days',
        'frequency',
        'end_condition_type',
        'end_date',
        'end_count',
        'send_automatically',
        'next_run_date',
        'last_run_date',
        'notes',
        'discount_type',
        'discount_value',
        'total_price',
        'customer_id',
        'vendor_id',
        'updated_by',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => RecurringInvoiceStatus::class,
        'send_automatically' => 'boolean',
        'due_after_days' => 'integer',
        'end_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_date' => 'date',
        'last_run_date' => 'date',
    ];

    /**
     * Get the notification routing information for the given driver.
     */
    public function routeNotificationFor(string $channel): mixed
    {
        if ($channel === 'mail') {
            return $this->customer->email;
        }

        return null;
    }

    /**
     * Get formatted invoice items.
     */
    public function getFormattedInvoiceItemsAttribute(): Collection
    {
        return $this->invoiceItems->map(function ($item) {
            $itemType = $this->getItemType($item);
            $formattedData = $itemType->getFormattedData($item);

            return (object) [
                'type_id' => $formattedData['id'],
                'type' => $formattedData['name'],
                'title' => $formattedData['title'],
                'description' => $item->description,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'amount' => $item->amount,
                'item_id' => $formattedData['item_id'] ?? false,
            ];
        });
    }

    /**
     * Get invoice item type.
     */
    private function getItemType($item): InvoiceItemType
    {
        return InvoiceItemType::CUSTOM;
    }

    /**
     * Get the formatted start date attribute.
     */
    public function getReadableStartDateAttribute(): string
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format('d M Y') : '';
    }

    /**
     * Get the formatted end date attribute.
     */
    public function getReadableEndDateAttribute(): string
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format('d M Y') : '';
    }

    /**
     * Get the calculated due date attribute.
     */
    public function getCalculatedDueDateAttribute(): ?Carbon
    {
        if (! $this->start_date || ! $this->due_after_days) {
            return null;
        }

        return Carbon::parse($this->start_date)->addDays($this->due_after_days);
    }

    /**
     * Get the formatted total price attribute.
     */
    public function getReadableTotalPriceAttribute(): string
    {
        if ($this->vendor?->currency) {
            return $this->vendor->currency.' '.MoneyHelper::format($this->total_price);
        }

        return MoneyHelper::print($this->total_price);
    }

    /**
     * Get the formatted discount price attribute.
     */
    public function getReadableDiscountPriceAttribute(): string
    {
        if ($this->vendor?->currency) {
            if ($this->discount_type === 'percentage') {
                $invoiceItems = $this->getFormattedInvoiceItemsAttribute();
                $itemsTotal = $invoiceItems->sum('amount');

                return $this->vendor->currency.' '.MoneyHelper::format($itemsTotal - $this->total_price);
            }

            return $this->vendor->currency.' '.MoneyHelper::format($this->discount_value);
        }

        return MoneyHelper::print($this->discount_value);
    }

    /**
     * Get the formatted sub total price attribute.
     */
    public function getReadableSubTotalPriceAttribute(): string
    {
        $value = array_sum(array_column($this->invoiceItems->toArray(), 'amount'));

        if ($this->vendor?->currency) {
            return $this->vendor->currency.' '.MoneyHelper::format($value);
        }

        return MoneyHelper::print($value);
    }

    /**
     * Get the payment due amount attribute.
     */
    public function getPaymentDueAmountAttribute(): string
    {
        $totalPaid = $this->payments->where('status', PaymentStatus::PAID)->sum('amount');

        $value = $this->total_price - $totalPaid;

        if ($this->vendor?->currency) {
            return $this->vendor->currency.' '.MoneyHelper::format($value);
        }

        return MoneyHelper::print($value);
    }

    /**
     * Model media collections.
     */
    public function registerMediaCollections(): void
    {
        //
    }

    /**
     * Define model methods with Has relations.
     */
    public function defineHasRelationships(): array
    {
        return ['payments'];
    }

    /**
     * Get the customer that owns the invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the vendor that owns the invoice.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the quotation that owns the invoice.
     */
    public function quotation(): HasOne
    {
        return $this->hasOne(Quotation::class);
    }

    /**
     * Get the invoice items associated with the recurring invoice.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(RecurringInvoiceItem::class);
    }

    /**
     * Get the invoices generated from this recurring invoice.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the payments assocbiated with the invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
