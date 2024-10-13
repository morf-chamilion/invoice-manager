<?php

namespace App\Enums;

use Illuminate\Support\Facades\Blade;

enum InvoiceItemType: int
{
    case HEADING = 0;
    case DESCRIPTION = 1;

    /**
     * Get the human readable name.
     */
    public function getName(): string
    {
        return match ($this) {
            self::HEADING => 'Heading',
            self::DESCRIPTION => 'Description',
            default => 'Not known',
        };
    }

    /**
     * Select options for rendering a dropdown.
     */
    public static function toSelectOptions(): array
    {
        return array_map(fn($enum) => (object) [
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
            self::HEADING->value => 'badge badge-light',
            self::DESCRIPTION->value => 'badge badge-warning',
        ];

        return Blade::render('<span class="{{ $class }}">{{ $status->getName() }}</span>', [
            'class' => $classes[$status->value],
            'status' => $status
        ]);
    }
}
