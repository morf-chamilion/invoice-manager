<?php

namespace App\Http\Resources\Admin\Invoice;

use App\Enums\InvoiceStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\Customer\CustomerRoutePath;
use App\RoutePaths\Admin\Invoice\InvoiceRoutePath;
use App\Services\InvoiceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class InvoiceIndexResource extends JsonResource implements HasDataTableInterface
{
	use HasDataTableTrait;

	/**
	 * Transform the resource into an array.
	 */
	public function transformRecords($records)
	{
		return collect($records)->map(function ($record) {
			return [
				$record->id,
				$this->number($record),
				$this->customer($record->customer),
				$record->readableDate,
				$this->dueDate($record),
				$record->readableTotalPrice,
				InvoiceStatus::toBadge($record->status),
				$this->actions($record),
			];
		})->all();
	}

	/**
	 * Render record actions.
	 */
	private function actions(
		Model $record,
		$show = InvoiceRoutePath::SHOW,
		$edit = InvoiceRoutePath::EDIT,
		$destroy = InvoiceRoutePath::DESTROY,
		$overdue = InvoiceRoutePath::OVERDUE
	): array {
		/** @var InvoiceService $invoiceService */
		$invoiceService = App::make(InvoiceService::class);

		$isDueInvoice = $invoiceService->isDueInvoice($record);

		$actions = [
			'show' => Gate::check($show) ? route($show, $record) : '',
			'edit' => Gate::check($edit) ? route($edit, $record) : '',
			'destroy' => Gate::check($destroy) ? route($destroy, $record) : '',
		];

		if ($isDueInvoice) {
			$actions['overdue'] = Gate::check($show) ? route($overdue, $record) : '';
		}

		if ($record->status === InvoiceStatus::COMPLETED) {
			$actions['edit'] = null;
		}

		return $actions;
	}

	/**
	 * Render number.
	 */
	private function number(Model $resource): string
	{
		/** @var InvoiceService $invoiceService */
		$invoiceService = App::make(InvoiceService::class);

		$isDueInvoice = $invoiceService->isDueInvoice($resource);

		return Blade::render('<a href="{{ $link }}" class="@if ($isDueInvoice) text-danger @endif">{{ $number }}</a>', [
			'link' => route(InvoiceRoutePath::EDIT, $resource),
			'number' => $resource->number,
			'isDueInvoice' => $isDueInvoice,
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
	 * Render due date.
	 */
	private function dueDate(Model $resource): string
	{
		/** @var InvoiceService $invoiceService */
		$invoiceService = App::make(InvoiceService::class);

		$isDueInvoice = $invoiceService->isDueInvoice($resource);

		return Blade::render('<div class="d-flex justify-content-between @if ($isDueInvoice) text-danger @endif">{{ $date }}</div>', [
			'date' => $resource->readableDueDate,
			'isDueInvoice' => $isDueInvoice,
		]);
	}
}
