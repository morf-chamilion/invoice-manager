<?php

namespace App\Http\Resources\Admin\Payment;

use App\Enums\PaymentStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\Customer\CustomerRoutePath;
use App\RoutePaths\Admin\Payment\PaymentRoutePath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class PaymentIndexResource extends JsonResource implements HasDataTableInterface
{
	use HasDataTableTrait;

	/**
	 * Transform the resource into an array.
	 */
	public function transformRecords($records)
	{
		return collect($records)->map(function ($record) {
			return [
				$this->number($record),
				$this->customer($record->customer),
				$record->readableDate,
				PaymentStatus::toBadge($record->status),
				$this->actions($record),
			];
		})->all();
	}

	/**
	 * Render number.
	 */
	private function number(Model $resource): string
	{
		return Blade::render('<a href="{{ $link }}">{{ $number }}</a>', [
			'link' => route(PaymentRoutePath::EDIT, $resource),
			'number' => $resource->number,
		]);
	}

	/**
	 * Render customer.
	 */
	protected static function customer(Model $customer): string
	{
		if (Gate::check(CustomerRoutePath::EDIT)) {
			return Blade::render('<a href="{{ $url }}" target="_blank">{{ $name }}</a>', [
				'url' => route(CustomerRoutePath::EDIT, $customer->id),
				'name' => $customer->name,
			]);
		}

		return Blade::render('{{ $name }}', [
			'name' => $customer->name,
		]);
	}

	/**
	 * Render record actions.
	 */
	private function actions(
		Model $record,
		$edit = PaymentRoutePath::EDIT,
		$destroy = PaymentRoutePath::DESTROY
	): array {
		return [
			'edit' => Gate::check($edit) ? route($edit, $record) : '',
			'destroy' => Gate::check($destroy) ? route($destroy, $record) : '',
		];
	}
}
