<?php

namespace App\Http\Resources\Admin\RecurringInvoice;

use App\Enums\RecurringInvoiceFrequency;
use App\Enums\RecurringInvoiceStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\Customer\CustomerRoutePath;
use App\RoutePaths\Admin\RecurringInvoice\RecurringInvoiceRoutePath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class RecurringInvoiceIndexResource extends JsonResource implements HasDataTableInterface
{
    use HasDataTableTrait;

    /**
     * Transform the resource into an array.
     */
    public function transformRecords($records)
    {
        return collect($records)->map(function ($record) {
            return [
                $this->customer($record->customer),
                $record->readableStartDate,
                RecurringInvoiceFrequency::from($record->frequency)->getName(),
                $record->readableEndDate,
                $record->readableTotalPrice,
                RecurringInvoiceStatus::toBadge($record->status),
                $this->actions($record),
            ];
        })->all();
    }

    /**
     * Render record actions.
     */
    private function actions(
        Model $record,
        $show = RecurringInvoiceRoutePath::SHOW,
        $edit = RecurringInvoiceRoutePath::EDIT,
        $destroy = RecurringInvoiceRoutePath::DESTROY,
    ): array {
        return [
            'show' => Gate::check($show) ? route($show, $record) : '',
            'edit' => Gate::check($edit) ? route($edit, $record) : '',
            'destroy' => Gate::check($destroy) ? route($destroy, $record) : '',
        ];
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
}
