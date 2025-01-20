<?php

namespace App\RoutePaths\Admin\Payment;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\PaymentService;

class PaymentRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected PaymentService $paymentService,
	) {}

	public const INDEX = 'admin.payment.index';

	public const CREATE = 'admin.payment.create';

	public const STORE = 'admin.payment.store';

	public const SHOW = 'admin.payment.show';

	public const EDIT = 'admin.payment.edit';

	public const UPDATE = 'admin.payment.update';

	public const DESTROY = 'admin.payment.destroy';

	public const INVOICE_INDEX = 'admin.payment.invoice.index';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->paymentService->modelName();
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'List' => self::INDEX,
			'Show' => [self::SHOW],
			'Create' => [self::CREATE, self::STORE, self::INVOICE_INDEX],
			'Edit' => [self::EDIT, self::UPDATE],
			'Delete' => self::DESTROY,
		];
	}
}
