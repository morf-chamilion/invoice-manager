<?php

namespace App\RoutePaths\Admin\Customer;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\CustomerService;

class CustomerRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected CustomerService $customerService,
	) {
	}

	public const INDEX = 'admin.customer.index';

	public const CREATE = 'admin.customer.create';

	public const STORE = 'admin.customer.store';

	public const EDIT = 'admin.customer.edit';

	public const UPDATE = 'admin.customer.update';

	public const DESTROY = 'admin.customer.destroy';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->customerService->modelName();
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'List' => self::INDEX,
			'Create' => [self::CREATE, self::STORE],
			'Edit' => [self::EDIT, self::UPDATE],
			'Delete' => self::DESTROY,
		];
	}
}
