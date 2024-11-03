<?php

namespace App\RoutePaths\Admin\Vendor;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\VendorService;

class VendorRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected VendorService $vendorService,
	) {}

	public const INDEX = 'admin.vendor.index';

	public const CREATE = 'admin.vendor.create';

	public const STORE = 'admin.vendor.store';

	public const EDIT = 'admin.vendor.edit';

	public const UPDATE = 'admin.vendor.update';

	public const DESTROY = 'admin.vendor.destroy';

	public const GENERAL_SETTING_EDIT = 'admin.vendor.general-setting.edit';

	public const GENERAL_SETTING_UPDATE = 'admin.vendor.general-setting.update';

	public const QUOTATION_SETTING_EDIT = 'admin.vendor.quotation-setting.edit';

	public const QUOTATION_SETTING_UPDATE = 'admin.vendor.quotation-setting.update';

	public const INVOICE_SETTING_EDIT = 'admin.vendor.invoice-setting.edit';

	public const INVOICE_SETTING_UPDATE = 'admin.vendor.invoice-setting.update';

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
			'General Setting Edit' => [self::GENERAL_SETTING_EDIT, self::GENERAL_SETTING_UPDATE],
			'Quotation Setting Edit' => [self::QUOTATION_SETTING_EDIT, self::QUOTATION_SETTING_UPDATE],
			'Invoice Setting Edit' => [self::INVOICE_SETTING_EDIT, self::INVOICE_SETTING_UPDATE],
		];
	}
}
