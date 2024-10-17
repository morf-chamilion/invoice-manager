<?php

namespace App\Enums;

use Illuminate\Support\Facades\Blade;

enum QuotationItemType: int
{
    case CUSTOM = 1;

    /**
     * Get the human readable name.
     */
    public function getName(): string
    {
        return match ($this) {
            self::CUSTOM => 'Custom',
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
            self::CUSTOM->value => 'badge badge-light',
        ];

        return Blade::render('<span class="{{ $class }}">{{ $status->getName() }}</span>', [
            'class' => $classes[$status->value],
            'status' => $status
        ]);
    }

    /**
     * Get formatted invoice item data.
     */
    public function getFormattedData($item): array
    {
        return match ($this) {
            self::CUSTOM => [
                'id' => $this->value,
                'name' => $this->getName(),
                'title' => $item->custom,
                'item_id' => null,
            ],
            default => [
                'id' => null,
                'name' => 'Unknown',
                'title' => '',
                'item_id' => null,
            ]
        };
    }
}
