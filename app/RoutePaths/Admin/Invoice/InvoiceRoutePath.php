<?php

namespace App\RoutePaths\Admin\Invoice;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\InvoiceService;

class InvoiceRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected InvoiceService $invoiceService,
	) {
	}

	public const INDEX = 'admin.invoice.index';

	public const CREATE = 'admin.invoice.create';

	public const STORE = 'admin.invoice.store';

	public const SHOW = 'admin.invoice.show';

	public const EDIT = 'admin.invoice.edit';

	public const UPDATE = 'admin.invoice.update';

	public const DESTROY = 'admin.invoice.destroy';

	public const DOWNLOAD = 'admin.invoice.download';

	public const OVERDUE = 'admin.invoice.overdue';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->invoiceService->modelName();
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'List' => self::INDEX,
			'Show' => [self::SHOW, self::OVERDUE, self::DOWNLOAD],
			'Create' => [self::CREATE, self::STORE],
			'Edit' => [self::EDIT, self::UPDATE],
			'Delete' => self::DESTROY,
		];
	}
}
