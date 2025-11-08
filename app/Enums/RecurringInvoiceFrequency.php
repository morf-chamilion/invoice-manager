<?php

namespace App\Enums;

enum RecurringInvoiceFrequency: string
{
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    /**
     * Get the human readable name.
     */
    public function getName(): string
    {
        return match ($this) {
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::QUARTERLY => 'Quarterly',
            self::YEARLY => 'Yearly',
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
