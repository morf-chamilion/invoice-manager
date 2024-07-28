<?php

namespace App\RoutePaths\Admin\Vendor;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\VendorService;

class VendorRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected VendorService $vendorService,
	) {
	}

	public const INDEX = 'admin.vendor.index';

	public const CREATE = 'admin.vendor.create';

	public const STORE = 'admin.vendor.store';

	public const EDIT = 'admin.vendor.edit';

	public const UPDATE = 'admin.vendor.update';

	public const DESTROY = 'admin.vendor.destroy';

	public const INVOICE_SETTING_EDIT = 'admin.vendor.invoice-setting.edit';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->vendorService->modelName();
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'List' => [self::INDEX],
			'Create' => [self::CREATE, self::STORE],
			'Edit' => [self::EDIT, self::UPDATE],
			'Delete' => [self::DESTROY],
			'Invoice Setting Edit' => [self::INVOICE_SETTING_EDIT, self::UPDATE],
		];
	}
}
