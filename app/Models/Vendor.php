<?php

namespace App\Models;

use App\Enums\VendorStatus;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Traits\HasCreatedBy;
use App\Models\Traits\HasUpdatedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vendor extends Model implements HasMedia, HasRelationsInterface
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
		'currency',
		'address',
		'bank_account_details',
		'footer_content',
		'invoice_number_prefix',
		'updated_by',
		'created_by',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'status' => VendorStatus::class,
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
	 * Model media collections.
	 */
	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('invoiceImage')
			->useDisk('media')
			->singleFile();
	}

	/**
	 * Interact with media.
	 */
	protected function logo(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->getMedia('logo'),
		);
	}

	/**
	 * Define model methods with Has relations.
	 */
	public function defineHasRelationships(): array
	{
		return ['users', 'invoices'];
	}

	/**
	 * Get the users items assocbiated with the vendor.
	 */
	public function users(): HasMany
	{
		return $this->hasMany(User::class);
	}

	/**
	 * Get the invoices items assocbiated with the vendor.
	 */
	public function invoices(): HasMany
	{
		return $this->hasMany(Invoice::class);
	}
}
