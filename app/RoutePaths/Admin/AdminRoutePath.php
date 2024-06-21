<?php

namespace App\RoutePaths\Admin;

class AdminRoutePath implements AdminRoutePathInterface
{
	public const DASHBOARD = 'admin.dashboard.index';

	public const PROFILE_EDIT = 'admin.profile.edit';

	public const PROFILE_UPDATE = 'admin.profile.update';

	public const PROFILE_DESTROY = 'admin.profile.destroy';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return __('dashboard');
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'Show' => self::DASHBOARD,
		];
	}
}
