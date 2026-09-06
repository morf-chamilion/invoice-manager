<?php

namespace App\RoutePaths\Admin\RecurringInvoice;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\RecurringInvoiceService;

class RecurringInvoiceRoutePath implements AdminRoutePathInterface
{
    public function __construct(
        protected RecurringInvoiceService $recurringInvoiceService,
    ) {}

    public const INDEX = 'admin.recurring-invoice.index';

    public const CREATE = 'admin.recurring-invoice.create';

    public const STORE = 'admin.recurring-invoice.store';

    public const SHOW = 'admin.recurring-invoice.show';

    public const EDIT = 'admin.recurring-invoice.edit';

    public const UPDATE = 'admin.recurring-invoice.update';

    public const DESTROY = 'admin.recurring-invoice.destroy';

    public const CUSTOMER_INDEX = 'admin.recurring-invoice.customer.index';

    public const CUSTOMER_STORE = 'admin.recurring-invoice.customer.store';

    public const CUSTOMER_NOTIFICATION = 'admin.recurring-invoice.customer.notification';

    /**
     * Name of the resource.
     */
    public function resourceName(): string
    {
        return $this->recurringInvoiceService->modelName();
    }

    /**
     * Associative mapping resource actions to route names.
     */
    public static function routeMappings(): array
    {
        return [
            'List' => self::INDEX,
            'Show' => [self::SHOW, self::CUSTOMER_NOTIFICATION],
            'Create' => [self::CREATE, self::STORE, self::CUSTOMER_INDEX, self::CUSTOMER_STORE],
            'Edit' => [self::EDIT, self::UPDATE],
            'Delete' => self::DESTROY,
        ];
    }
}
