<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Services\MediaService;
use App\Services\Traits\HandlesMedia;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class PaymentRepository extends BaseRepository
{
	use HandlesMedia;

	public function __construct(
		private Payment $payment,
		private MediaService $mediaService,
	) {
		parent::__construct($payment);
	}

	/**
	 * Get all payments.
	 */
	public function getAll(): Collection
	{
		return $this->payment::all();
	}

	/**
	 * Get the specified payment.
	 */
	public function getById(int $paymentId): ?Payment
	{
		return $this->payment::find($paymentId);
	}

	/**
	 * Get the payment by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?Payment
	{
		return $this->payment::where($columnName, $value)->first();
	}

	/**
	 * Delete a specific payment.
	 */
	public function delete(int $paymentId): bool|QueryException
	{
		$payment = $this->getById($paymentId);

		$this->checkModelHasParentRelations($payment);

		try {
			return $payment->delete($paymentId);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage());

			return false;
		}
	}

	/**
	 * Create a new payment.
	 */
	public function create(array $attributes): Payment
	{
		$payment = $this->payment::create($attributes);

		$payment->number = $payment->id;
		$payment->save();

		return $payment;
	}

	/**
	 * Update an existing payment.
	 */
	public function update(int $paymentId, array $newAttributes): bool
	{
		return $this->payment::whereId($paymentId)
			->update($newAttributes);
	}

	/**
	 * Update an existing payment settings.
	 */
	public function updateSettings(int $paymentId, array $newAttributes): bool
	{
		$payment = $this->payment::findOrFail($paymentId)
			->fill($newAttributes);

		return $payment->save();
	}
}
