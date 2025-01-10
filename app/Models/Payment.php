<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Traits\HasCreatedBy;
use App\Models\Traits\HasUpdatedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Payment extends Model implements HasMedia, HasRelationsInterface
{
	use HasFactory, InteractsWithMedia, HasCreatedBy, HasUpdatedBy, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'status',
		'name',
		'date',
		'amount',
		'notes',
		'method',
		'vendor_id',
		'invoice_id',
		'customer_id',
		'updated_by',
		'created_by',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'status' => PaymentStatus::class,
		'method' => PaymentMethod::class,
		'date' => 'datetime',
		'data' => 'array',
	];

	/**
	 * Interact with the payment number.
	 */
	protected function number(): Attribute
	{
		return Attribute::make(
			set: function (string $value): string {
				$formattedNumber = 'PAY/' . str_pad($this->id, 4, '0', STR_PAD_LEFT);

				return $this->attributes['number'] = $formattedNumber;
			},
		);
	}

	/**
	 * Get the formatted date attribute.
	 */
	public function getReadableDateAttribute(): string
	{
		return Carbon::parse($this->date)->format('d M Y');
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
	 * Get the customer that owns the payment.
	 */
	public function customer(): BelongsTo
	{
		return $this->belongsTo(Customer::class);
	}

	/**
	 * Get the vendor that owns the payment.
	 */
	public function vendor(): BelongsTo
	{
		return $this->belongsTo(Vendor::class);
	}

	/**
	 * Get the invoice that owns the payment.
	 */
	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}
}
