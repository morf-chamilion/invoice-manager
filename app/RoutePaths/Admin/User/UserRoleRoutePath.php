<?php

namespace App\RoutePaths\Admin\User;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\UserRoleService;

class UserRoleRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected UserRoleService $userRoleService,
	) {
	}

	public const INDEX = 'admin.user.role.index';

	public const CREATE = 'admin.user.role.create';

	public const STORE = 'admin.user.role.store';

	public const EDIT = 'admin.user.role.edit';

	public const UPDATE = 'admin.user.role.update';

	public const DESTROY = 'admin.user.role.destroy';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->userRoleService->modelName();
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
		];
	}
}
