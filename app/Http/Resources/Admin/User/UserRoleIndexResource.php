<?php

namespace App\Http\Resources\Admin\User;

use App\Enums\UserRoleStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\User\UserRoleRoutePath;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRoleIndexResource extends JsonResource implements HasDataTableInterface
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
                UserRoleStatus::toBadge($record->status),
                [
                    'edit' => route(UserRoleRoutePath::EDIT, $record),
                    'destroy' => route(UserRoleRoutePath::DESTROY, $record),
                ],
            ];
        })->all();
    }
}
