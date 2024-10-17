<?php

namespace App\Repositories;

use App\Enums\QuotationItemType;
use App\Enums\QuotationStatus;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Services\MediaService;
use App\Services\Traits\HandlesMedia;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class QuotationRepository extends BaseRepository
{
	use HandlesMedia;

	public function __construct(
		private Quotation $quotation,
		private MediaService $mediaService,
	) {
		parent::__construct($quotation);
	}

	/**
	 * Get all quotations.
	 */
	public function getAll(): Collection
	{
		return $this->quotation::all();
	}

	/**
	 * Get the specified quotation.
	 */
	public function getById(int $quotationId): ?Quotation
	{
		return $this->quotation::find($quotationId);
	}

	/**
	 * Get the quotation by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?Quotation
	{
		return $this->quotation::where($columnName, $value)->first();
	}

	/**
	 * Get quotation that belongs to a customer.
	 */
	public function getCustomerQuotation(int $quotationId, int $customerId): ?Quotation
	{
		return $this->quotation->where('id', $quotationId)
			->where('customer_id', $customerId)
			->where('status', '!=', QuotationStatus::DRAFT)
			->first();
	}

	/**
	 * Get quotations that belongs to a customer.
	 */
	public function getCustomerQuotations(int $customerId): ?Collection
	{
		return $this->quotation->where('customer_id', $customerId)
			->where('status', '!=', QuotationStatus::DRAFT)
			->get();
	}

	/**
	 * Get the last quotation for the vendor.
	 */
	public function getLastVendorQuotation(int $vendorId): ?Quotation
	{
		return $this->quotation->where('vendor_id', $vendorId)
			->orderBy('vendor_quotation_number', 'desc')
			->first();
	}

	/**
	 * Delete a specific quotation.
	 */
	public function delete(int $quotationId): bool|QueryException
	{
		$quotation = $this->getById($quotationId);

		$this->checkModelHasParentRelations($quotation);

		try {
			return $quotation->delete($quotationId);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage());

			return false;
		}
	}

	/**
	 * Create a new quotation.
	 */
	public function create(array $attributes): Quotation
	{
		$quotationItems = Arr::pull($attributes, 'quotation_items');

		$quotation = $this->quotation::create($attributes);

		$lastQuotation = $this->getLastVendorQuotation($attributes['vendor_id']);
		$quotation->vendor_quotation_number = $lastQuotation ? ++$lastQuotation->vendor_quotation_number : 1;
		$quotation->number = $quotation->id;

		$totalPrice = 0;

		if ($quotationItems) {
			$quotation->quotationItems()->delete();

			$totalPrice = $this->syncQuotationItems($quotation, $quotationItems);
			$quotation->total_price = $totalPrice;
		}

		$quotation->total_price = $totalPrice;

		$quotation->save();

		return $quotation;
	}

	/**
	 * Update an existing quotation.
	 */
	public function update(int $quotationId, array $newAttributes): bool
	{
		$quotationItems = Arr::pull($newAttributes, 'quotation_items');

		$quotation = $this->quotation::findOrFail($quotationId);

		$updated = $quotation->update($newAttributes);

		$totalPrice = 0;

		if ($quotationItems) {
			$totalPrice = $this->syncQuotationItems($quotation, $quotationItems);
			$quotation->total_price = $totalPrice;

			$quotation->save();
		} else {
			$quotation->quotationItems()->delete();
		}

		return $updated;
	}

	/**
	 * Update quotation items and calculate total price.
	 */
	private function syncQuotationItems($quotation, array|object $quotationItems): float
	{
		$quotation->quotationItems()->delete();

		$totalPrice = 0;

		foreach ($quotationItems as $quotationItem) {
			$item = new QuotationItem;
			$item->quotation_id = $quotation->id;

			$this->setQuotationItemType($item, $quotationItem);

			$item->description = is_array($quotationItem) ? $quotationItem['description'] : $quotationItem->description;
			$item->quantity = is_array($quotationItem) ? $quotationItem['quantity'] : $quotationItem->quantity;
			$item->unit_price = is_array($quotationItem) ? $quotationItem['unit_price'] : $quotationItem->unit_price;
			$item->amount = is_array($quotationItem) ? $quotationItem['amount'] : $quotationItem->amount;

			$item->save();

			$totalPrice += $item->amount;
		}

		return $totalPrice;
	}

	/**
	 * Set the item type for an quotation item.
	 */
	private function setQuotationItemType(QuotationItem $item, array|object $quotationItem)
	{
		$typeId = is_array($quotationItem) ? $quotationItem['type_id'] : $quotationItem->type_id;
		$itemId = is_array($quotationItem) ? $quotationItem['item_id'] : $quotationItem->item_id;
		$itemTitle = is_array($quotationItem) ? $quotationItem['title'] : $quotationItem->title;
		$itemType = QuotationItemType::from($typeId);

		match ($itemType) {
			QuotationItemType::CUSTOM => $item->custom = $itemTitle,
		};
	}
}
