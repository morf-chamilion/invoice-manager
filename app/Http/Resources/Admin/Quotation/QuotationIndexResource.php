<?php

namespace App\Http\Resources\Admin\Quotation;

use App\Enums\QuotationStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\Customer\CustomerRoutePath;
use App\RoutePaths\Admin\Quotation\QuotationRoutePath;
use App\Services\QuotationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class QuotationIndexResource extends JsonResource implements HasDataTableInterface
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
				$this->validUntilDate($record),
				$record->readableTotalPrice,
				QuotationStatus::toBadge($record->status),
				$this->actions($record),
			];
		})->all();
	}

	/**
	 * Render record actions.
	 */
	private function actions(
		Model $record,
		$show = QuotationRoutePath::SHOW,
		$edit = QuotationRoutePath::EDIT,
		$destroy = QuotationRoutePath::DESTROY,
	): array {
		$actions = [
			'show' => Gate::check($show) ? route($show, $record) : '',
			'edit' => Gate::check($edit) ? route($edit, $record) : '',
			'destroy' => Gate::check($destroy) ? route($destroy, $record) : '',
		];

		if ($record->status === QuotationStatus::CONVERTED) {
			$actions['edit'] = null;
		}

		return $actions;
	}

	/**
	 * Render number.
	 */
	private function number(Model $resource): string
	{
		return Blade::render('<a href="{{ $link }}">{{ $number }}</a>', [
			'link' => route(QuotationRoutePath::EDIT, $resource),
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
	 * Render valid until date.
	 */
	private function validUntilDate(Model $resource): string
	{
		/** @var QuotationService $quotationService */
		$quotationService = App::make(QuotationService::class);

		$isDueQuotation = $quotationService->isPastValidUntilQuotation($resource);

		return Blade::render('<div class="d-flex justify-content-between @if ($isDueQuotation) text-danger @endif">{{ $date }}</div>', [
			'date' => $resource->readableValidUntilDate,
			'isDueQuotation' => $isDueQuotation,
		]);
	}
}
