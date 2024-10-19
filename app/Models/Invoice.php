<?php

namespace App\Models;

use App\Enums\InvoiceItemType;
use App\Enums\InvoicePaymentMethod;
use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Helpers\MoneyHelper;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Traits\HasCreatedBy;
use App\Models\Traits\HasUpdatedBy;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;
use App\RoutePaths\Front\Invoice\InvoiceRoutePath;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia, HasRelationsInterface
{
	use HasFactory, InteractsWithMedia, HasCreatedBy, HasUpdatedBy, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'status',
		'date',
		'due_date',
		'number',
		'customer_id',
		'total_price',
		'notes',
		'payment_method',
		'payment_status',
		'payment_data',
		'payment_data->amount',
		'payment_data->transaction_id',
		'payment_data->reference',
		'payment_date',
		'vendor_id',
		'vendor_invoice_number',
		'updated_by',
		'created_by',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'status' => InvoiceStatus::class,
		'payment_status' => InvoicePaymentStatus::class,
		'payment_method' => InvoicePaymentMethod::class,
		'payment_date' => 'datetime',
		'payment_data' => 'array',
	];

	/**
	 * Get the notification routing information for the given driver.
	 */
	public function routeNotificationFor(string $channel): mixed
	{
		if ($channel === 'mail') {
			return $this->customer->email;
		}
	}

	/**
	 * Interact with the invoice number.
	 */
	protected function number(): Attribute
	{
		return Attribute::make(
			set: function (string $value): string {
				$id = str_pad($this->vendor_invoice_number, 3, '0', STR_PAD_LEFT);
				$currentYear = now()->format('y');
				$currentMonth = now()->format('m');
				$vendorPrefix = $this->vendor->reference_number_prefix ?? 'TMP';
				$formattedNumber = "$vendorPrefix/INV/{$currentYear}/{$currentMonth}/{$id}";

				return $this->attributes['number'] = $formattedNumber;
			},
		);
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
	 * Get the show invoice link.
	 */
	protected function getShowLinkAttribute(): string
	{
		$sessionId = Crypt::encryptString($this->id);

		return route(InvoiceRoutePath::SHOW, ['id' => $sessionId]);
	}

	/**
	 * Get the checkout link for the invoice.
	 */
	protected function getCheckoutLinkAttribute(): string
	{
		$sessionId = Crypt::encryptString($this->id);

		return route(CheckoutRoutePath::SHOW, ['id' => $sessionId]);
	}

	/**
	 * Get the formatted date attribute.
	 */
	public function getReadableDateAttribute(): string
	{
		return Carbon::parse($this->date)->format('d M Y');
	}

	/**
	 * Get the formatted due date attribute.
	 */
	public function getReadableDueDateAttribute(): string
	{
		return Carbon::parse($this->due_date)->format('d M Y');
	}

	/**
	 * Get the formatted total price attribute.
	 */
	public function getReadableTotalPriceAttribute(): string
	{
		return MoneyHelper::print($this->total_price);
	}

	/**
	 * Model media collections.
	 */
	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('payment_reference_receipt')
			->useDisk('media')
			->singleFile();
	}

	/**
	 * Interact with media.
	 */
	protected function paymentReferenceReceipt(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->getMedia('payment_reference_receipt'),
		);
	}

	/**
	 * Define model methods with Has relations.
	 */
	public function defineHasRelationships(): array
	{
		return [];
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
	 * Get the invoice items assocbiated with the invoice.
	 */
	public function invoiceItems(): HasMany
	{
		return $this->hasMany(InvoiceItem::class);
	}
}
