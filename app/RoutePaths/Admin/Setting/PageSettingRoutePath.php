<?php

namespace App\RoutePaths\Admin\Setting;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\SettingService;

class PageSettingRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected SettingService $settingService,
	) {
	}

	public const HOME = 'admin.page.home';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return __('page');
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'Edit Home' => [self::HOME],
		];
	}
}
