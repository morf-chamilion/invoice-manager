<?php

namespace App\Models;

use App\Enums\QuotationItemType;
use App\Enums\QuotationStatus;
use App\Helpers\MoneyHelper;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Traits\HasCreatedBy;
use App\Models\Traits\HasUpdatedBy;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;
use App\RoutePaths\Front\Quotation\QuotationRoutePath;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Quotation extends Model implements HasMedia, HasRelationsInterface
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
		'number',
		'customer_id',
		'total_price',
		'notes',
		'vendor_id',
		'invoice_id',
		'vendor_quotation_number',
		'updated_by',
		'created_by',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'status' => QuotationStatus::class,
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
	 * Interact with the quotation number.
	 */
	protected function number(): Attribute
	{
		return Attribute::make(
			set: function (string $value): string {
				$id = str_pad($this->vendor_quotation_number, 3, '0', STR_PAD_LEFT);
				$currentYear = now()->format('y');
				$currentMonth = now()->format('m');
				$vendorPrefix = $this->vendor->reference_number_prefix;
				$formattedNumber = "$vendorPrefix/QUO/{$currentYear}/{$currentMonth}/{$id}";

				return $this->attributes['number'] = $formattedNumber;
			},
		);
	}

	/**
	 * Get formatted quotation items.
	 */
	public function getFormattedQuotationItemsAttribute(): Collection
	{
		return $this->quotationItems->map(function ($item) {
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
	 * Get quotation item type.
	 */
	private function getItemType($item): QuotationItemType
	{
		return QuotationItemType::CUSTOM;
	}

	/**
	 * Get the show quotation link.
	 */
	protected function getShowLinkAttribute(): string
	{
		$sessionId = Crypt::encryptString($this->id);

		return route(QuotationRoutePath::SHOW, ['id' => $sessionId]);
	}

	/**
	 * Get the checkout link for the quotation.
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
	 * Get the formatted total price attribute.
	 */
	public function getReadableTotalPriceAttribute(): string
	{
		return MoneyHelper::print($this->total_price);
	}

	/**
	 * Model media collections.
	 */
	public function registerMediaCollections(): void {}

	/**
	 * Define model methods with Has relations.
	 */
	public function defineHasRelationships(): array
	{
		return [];
	}

	/**
	 * Get the customer that owns the quotation.
	 */
	public function customer(): BelongsTo
	{
		return $this->belongsTo(Customer::class);
	}

	/**
	 * Get the vendor that owns the quotation.
	 */
	public function vendor(): BelongsTo
	{
		return $this->belongsTo(Vendor::class);
	}

	/**
	 * Get the invoice that owns the quotation.
	 */
	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}

	/**
	 * Get the quotation items assocbiated with the quotation.
	 */
	public function quotationItems(): HasMany
	{
		return $this->hasMany(QuotationItem::class);
	}
}
