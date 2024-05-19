<?php

namespace App\Enums;

use Illuminate\Support\Facades\Blade;

enum InvoiceStatus: int
{
    case DRAFT = 0;
    case ACTIVE = 1;
    case COMPLETED = 3;

    /**
     * Get the human readable name.
     */
    public function getName(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
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
            self::DRAFT->value => 'badge badge-light',
            self::ACTIVE->value => 'badge badge-success',
            self::COMPLETED->value => 'badge badge-info',
        ];

        return Blade::render('<span class="{{ $class }}">{{ $status->getName() }}</span>', [
            'class' => $classes[$status->value],
            'status' => $status
        ]);
    }
}
