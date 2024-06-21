<?php

namespace App\Http\Resources\Admin\Vendor;

use App\Enums\VendorStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\Vendor\VendorRoutePath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class VendorIndexResource extends JsonResource implements HasDataTableInterface
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
                VendorStatus::toBadge($record->status),
                $this->actions($record),
            ];
        })->all();
    }

    /**
     * Render record actions.
     */
    private function actions(
        Model $record,
        $edit = VendorRoutePath::EDIT,
        $destroy = VendorRoutePath::DESTROY
    ): array {
        return [
            'edit' => Gate::check($edit) ? route($edit, $record) : '',
            'destroy' => Gate::check($destroy) ? route($destroy, $record) : '',
        ];
    }
}
