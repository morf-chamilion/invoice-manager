<?php

namespace App\RoutePaths\Admin\Setting;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\SettingService;

class SettingRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected SettingService $settingService,
	) {
	}

	public const GENERAL = 'admin.setting.general';

	public const MAIL = 'admin.setting.mail';

	public const INVOICE = 'admin.setting.invoice';

	public const STORE = 'admin.setting.store';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->settingService->modelName();
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'Edit General' => [self::GENERAL],
			'Edit Mail' => [self::MAIL],
			'Edit Invoice' => [self::INVOICE],
		];
	}
}
