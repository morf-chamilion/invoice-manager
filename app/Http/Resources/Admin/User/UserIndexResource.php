<?php

namespace App\Http\Resources\Admin\User;

use App\Enums\UserStatus;
use App\Http\Resources\HasDataTableInterface;
use App\Http\Resources\HasDataTableTrait;
use App\RoutePaths\Admin\User\UserRoleRoutePath;
use App\RoutePaths\Admin\User\UserRoutePath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class UserIndexResource extends JsonResource implements HasDataTableInterface
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
                $record->email,
                $this->userRoleBadges($record->roles),
                UserStatus::toBadge($record->status),
                $this->actions($record),
            ];
        })->all();
    }

    /**
     * Render user role badges.
     */
    protected static function userRoleBadges($roles): string
    {
        return $roles->map(function ($role) {
            return Blade::render('<a class="badge badge-secondary" href="{{ $url }}">{{ $name }}</a>', [
                'name' => $role->name,
                'url' => route(UserRoleRoutePath::EDIT, $role->id),
            ]);
        })->implode(' ');
    }

    /**
     * Render record actions.
     */
    private function actions(
        Model $record,
        $edit = UserRoutePath::EDIT,
        $destroy = UserRoutePath::DESTROY
    ): array {
        return [
            'edit' => Gate::check($edit) ? route($edit, $record) : '',
            'destroy' => Gate::check($destroy) ? route($destroy, $record) : '',
        ];
    }
}
