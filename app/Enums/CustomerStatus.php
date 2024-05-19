<?php

namespace App\Enums;

use Illuminate\Support\Facades\Blade;

enum CustomerStatus: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;

    /**
     * Get the human readable name.
     */
    public function getName(): string
    {
        return match ($this) {
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
            default => 'Not known',
        };
    }

    /**
     * Select options for rendering a dropdown.
     */
    public static function toSelectOptions(): array
    {
        return array_map(fn ($enum) => (object) [
            'name' => $enum->getName(),
            'value' => $enum->value
        ], self::cases());
    }

    /**
     * Bootstrap badge HTML representation.
     */
    public static function toBadge(Self $status): string
    {
        $classes = [
            self::INACTIVE->value => 'badge badge-danger',
            self::ACTIVE->value => 'badge badge-success',
        ];

        return Blade::render('<span class="{{ $class }}">{{ $status->getName() }}</span>', [
            'class' => $classes[$status->value],
            'status' => $status
        ]);
    }
}
