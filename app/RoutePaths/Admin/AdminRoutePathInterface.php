<?php

namespace App\RoutePaths\Admin;

use App\RoutePaths\RoutePathInterface;

interface AdminRoutePathInterface extends RoutePathInterface
{
	/**
	 * Associative mapping of actions to route names.
	 */
	public static function routeMappings(): array;
}
