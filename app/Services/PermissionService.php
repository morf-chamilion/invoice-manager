<?php

namespace App\Services;

use App\Models\Permission;
use App\Repositories\PermissionRepository;
use App\RoutePaths\Admin\AdminRoutePathInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Exception;

class PermissionService extends BaseService
{
	protected array $routePaths;

	public function __construct(
		private PermissionRepository $permissionRepository,
	) {
		parent::__construct($permissionRepository);
	}

	/**
	 * Bind route paths from container.
	 */
	public function setRoutePaths(array $routePaths): void
	{
		$this->routePaths = $routePaths;
	}

	/**
	 * Get route paths from container.
	 */
	public function getRoutePaths(): array
	{
		return $this->routePaths;
	}

	/**
	 * Get all permissions.
	 */
	public function getAllPermissions(): Collection
	{
		return $this->permissionRepository->getAll();
	}

	/**
	 * Create a new permission.
	 */
	public function createPermission(array $attributes): Permission
	{
		return $this->permissionRepository->create($attributes);
	}

	/**
	 * Get the specified permission.
	 */
	public function getPermission(int $permissionId): ?Permission
	{
		return $this->permissionRepository->getById($permissionId);
	}

	/**
	 * Get the specified permission attribute.
	 */
	public function getPermissionWhere(string $columnName, mixed $value): ?Permission
	{
		return $this->permissionRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Delete a specific permission.
	 */
	public function deletePermission(int $permissionId): int
	{
		return $this->permissionRepository->delete($permissionId);
	}

	/**
	 * Update an existing permission.
	 */
	public function updatePermission(int $permissionId, array $newAttributes): bool
	{
		return $this->permissionRepository->update($permissionId, $newAttributes);
	}

	/**
	 * Truncate the permissions table.
	 * 
	 * This is an unsafe operation that temporarily disables foreign 
	 * key constraints to truncate the table.
	 */
	public function truncate(): bool | Exception
	{
		try {
			Schema::disableForeignKeyConstraints();
			$this->permissionRepository->getModel()->truncate();
		} catch (Exception $e) {
			throw new Exception("Error truncating permissions table {$e->getMessage()}");

			return false;
		} finally {
			Schema::enableForeignKeyConstraints();
		}

		return true;
	}

	/**
	 * Get grouped permissions by resources and actions.
	 */
	public function getPermissionGroups(): array | false
	{
		$permissions = $this->permissionRepository->getAll();

		if (!$permissions) return false;

		$groups = [];

		$permissions->map(function ($permission) use (&$groups) {
			return $groups[$permission->resource][$permission->action][] = $permission;
		});

		return $groups;
	}

	/**
	 * Get the formatted route mappings.
	 */
	public function getFormattedMappings(): array
	{
		$result = [];

		foreach ($this->routePaths as $routePath) {
			if ($routePath instanceof AdminRoutePathInterface) {
				$resourceName = $routePath->resourceName();
				$routeMappings = $routePath->routeMappings();

				$result[$resourceName] = [];

				foreach ($routeMappings as $action => $paths) {
					$result[$resourceName][$action] = is_array($paths) ? $paths : [$paths];
				}
			}
		}

		return $result;
	}

	/**
	 * Get all the route names.
	 */
	public function getAllRouteNames(): array
	{
		$routeNames = [];

		foreach ($this->getFormattedMappings() as $value) {
			$this->collectRouteNames($value, $routeNames);
		}

		return $routeNames;
	}

	/**
	 * Recursively collect route names.
	 */
	private function collectRouteNames($value, array &$routeNames): void
	{
		if (is_array($value)) {
			foreach ($value as $subValue) {
				$this->collectRouteNames($subValue, $routeNames);
			}
		} elseif (is_string($value) && strpos($value, '.') !== false) {
			$routeNames[] = $value;
		}
	}
}
