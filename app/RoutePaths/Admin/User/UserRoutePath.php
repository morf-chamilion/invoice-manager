<?php

namespace App\RoutePaths\Admin\User;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\UserService;

class UserRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected UserService $userService,
	) {
	}

	public const INDEX = 'admin.user.index';

	public const CREATE = 'admin.user.create';

	public const STORE = 'admin.user.store';

	public const EDIT = 'admin.user.edit';

	public const UPDATE = 'admin.user.update';

	public const DESTROY = 'admin.user.destroy';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->userService->modelName();
	}

	/**
	 * Associative mapping of actions to route names.
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
