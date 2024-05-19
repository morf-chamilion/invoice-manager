<?php

namespace App\Models;

use App\Enums\UserRoleStatus;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Traits\HasCreatedBy;
use App\Models\Traits\HasUpdatedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class UserRole extends SpatieRole implements HasRelationsInterface
{
    use HasFactory, HasCreatedBy, HasUpdatedBy;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => UserRoleStatus::class,
    ];

    /**
     * Get human readable name.
     */
    public function getHumanReadableNameAttribute(): string
    {
        return $this->permissions;
    }

    /**
     * Define model methods with Has relations.
     */
    public function defineHasRelationships(): array
    {
        return ['users'];
    }

    /**
     * Scope a query to only include active user roles.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', UserRoleStatus::ACTIVE);
    }
}
