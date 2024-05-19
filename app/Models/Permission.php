<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    /**
     * Get human readable name.
     */
    public function getHumanReadableNameAttribute(): string
    {
        return Str::of($this->name)
            ->remove('admin')
            ->replace('.', ' ')
            ->title();
    }
}
