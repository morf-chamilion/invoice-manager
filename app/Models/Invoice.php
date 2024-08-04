<?php

namespace App\Models;

use App\Enums\InvoicePaymentMethod;
use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Helpers\MoneyHelper;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Traits\HasCreatedBy;
use App\Models\Traits\HasUpdatedBy;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
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
				$id = str_pad($this->vendor_invoice_number, 5, '0', STR_PAD_LEFT);
				$currentYear = now()->format('y');
				$currentMonth = now()->format('m');
				$vendorPrefix = $this->vendor->invoice_number_prefix;
				$formattedNumber = "$vendorPrefix/{$currentYear}/{$currentMonth}/{$id}";

				return $this->attributes['number'] = $formattedNumber;
			},
		);
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
		$this->addMediaCollection('invoiceImage')
			->useDisk('media')
			->singleFile();
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
	 * Get the invoice items assocbiated with the invoice.
	 */
	public function invoiceItems(): HasMany
	{
		return $this->hasMany(InvoiceItem::class);
	}
}
