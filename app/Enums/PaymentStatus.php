<?php

namespace App\Enums;

use Illuminate\Support\Facades\Blade;

enum PaymentStatus: int
{
    case PAID = 1;
    case DECLINED = 0;

    /**
     * Get the human readable name.
     */
    public function getName(): string
    {
        return match ($this) {
            self::PAID => 'Paid',
            self::DECLINED => 'Declined',
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
    public static function toBadge(self $status): string
    {
        $classes = [
            self::PAID->value => 'badge badge-success',
            self::DECLINED->value => 'badge badge-danger',
        ];

        return Blade::render('<span class="{{ $class }}">{{ $status->getName() }}</span>', [
            'class' => $classes[$status->value],
            'status' => $status
        ]);
    }
}
