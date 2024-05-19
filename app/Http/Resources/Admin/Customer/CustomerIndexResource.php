<?php

namespace App\Http\Resources\Admin\Customer;

use App\Enums\CustomerStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\Customer\CustomerRoutePath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class CustomerIndexResource extends JsonResource implements HasDataTableInterface
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
                $record->name,
                $this->emailLink($record),
                $this->phoneLink($record),
                CustomerStatus::toBadge($record->status),
                $this->actions($record),
            ];
        })->all();
    }

    /**
     * Render resource link.
     */
    private static function phoneLink(Model $resource): string
    {
        return Blade::render('<a href="tel:{{ $url }}">{{ $title }}</a>', [
            'url' => $resource->phone,
            'title' => $resource->phone,
        ]);
    }

    /**
     * Render resource link.
     */
    private static function emailLink(Model $resource): string
    {
        return Blade::render('<a href="mailto:{{ $url }}">{{ $title }}</a>', [
            'url' => $resource->email,
            'title' => $resource->email,
        ]);
    }

    /**
     * Render record actions.
     */
    private function actions(
        Model $record,
        $edit = CustomerRoutePath::EDIT,
        $destroy = CustomerRoutePath::DESTROY
    ): array {
        return [
            'edit' => Gate::check($edit) ? route($edit, $record) : '',
            'destroy' => Gate::check($destroy) ? route($destroy, $record) : '',
        ];
    }
}
