<?php

namespace App\RoutePaths\Admin\Quotation;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\QuotationService;

class QuotationRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected QuotationService $quotationService,
	) {}

	public const INDEX = 'admin.quotation.index';

	public const CREATE = 'admin.quotation.create';

	public const STORE = 'admin.quotation.store';

	public const SHOW = 'admin.quotation.show';

	public const EDIT = 'admin.quotation.edit';

	public const UPDATE = 'admin.quotation.update';

	public const DESTROY = 'admin.quotation.destroy';

	public const DOWNLOAD = 'admin.quotation.download';

	public const CUSTOMER_INDEX = 'admin.quotation.customer.index';

	public const CUSTOMER_STORE = 'admin.quotation.customer.store';

	public const CUSTOMER_NOTIFICATION = 'admin.quotation.customer.notification';

	public const INVOICE_GENERATE = 'admin.quotation.invoice.generate';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->quotationService->modelName();
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'List' => self::INDEX,
			'Show' => [self::SHOW, self::DOWNLOAD, self::CUSTOMER_NOTIFICATION],
			'Create' => [self::CREATE, self::STORE, self::CUSTOMER_INDEX, self::CUSTOMER_STORE],
			'Edit' => [self::EDIT, self::UPDATE, self::INVOICE_GENERATE],
			'Delete' => self::DESTROY,
		];
	}
}
