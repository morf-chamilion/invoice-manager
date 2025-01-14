<?php

namespace App\Enums;

use Illuminate\Support\Facades\Blade;

enum PaymentMethod: int
{
    case CARD = 0;
    case CASH = 1;
    case BANK_TRANSFER = 2;
    case CHECK = 3;

    /**
     * Get the human readable name.
     */
    public function getName(): string
    {
        return match ($this) {
            self::CARD => 'Card',
            self::CASH => 'Cash',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CHECK => 'Check',
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
    public static function toBadge(Self $method): string
    {
        $classes = [
            self::CARD->value => 'badge badge-info',
            self::CASH->value => 'badge badge-success',
            self::BANK_TRANSFER->value => 'badge badge-info',
            self::CHECK->value => 'badge badge-dark',
        ];

        return Blade::render('<span class="{{ $class }}">{{ $method->getName() }}</span>', [
            'class' => $classes[$method->value],
            'method' => $method
        ]);
    }
}
