<?php

namespace App\Models;

use App\Enums\InvoiceItemType;
use App\Helpers\MoneyHelper;
use App\Models\Interfaces\HasRelationsInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class InvoiceItem extends Model implements HasRelationsInterface
{
	use HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'invoice_id',
		'custom',
		'description',
		'unit_price',
		'quantity',
		'amount',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'type' => InvoiceItemType::class,
	];

	/**
	 * Indicates if the model should be timestamped.
	 */
	public $timestamps = false;

	/**
	 * Get the formatted unit price attribute.
	 */
	public function getReadableUnitPriceAttribute(): string
	{
		if (!$this->unit_price) {
			return MoneyHelper::print(0);
		}

		return MoneyHelper::print($this->unit_price);
	}

	/**
	 * Get the formatted amount attribute.
	 */
	public function getReadableAmountAttribute(): string
	{
		if (!$this->amount) {
			return MoneyHelper::print(0);
		}

		return MoneyHelper::print($this->amount);
	}

	/**
	 * Define model methods with Has relations.
	 */
	public function defineHasRelationships(): array
	{
		return [];
	}

	/**
	 * Get the invoice that owns the invoice.
	 */
	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}
}
