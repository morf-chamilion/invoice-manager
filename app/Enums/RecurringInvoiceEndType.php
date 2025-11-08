<?php

namespace App\Enums;

enum RecurringInvoiceEndType: string
{
    case DATE = 'date';
    case COUNT = 'count';

    /**
     * Get the human readable name.
     */
    public function getName(): string
    {
        return match ($this) {
            self::DATE => 'By Date',
            self::COUNT => 'By Count',
        };
    }

    /**
     * Select options for rendering a dropdown.
     */
    public static function toSelectOptions(): array
    {
        return array_map(static fn (self $enum) => (object) [
            'name' => $enum->getName(),
            'value' => $enum->value,
        ], self::cases());
    }
}
