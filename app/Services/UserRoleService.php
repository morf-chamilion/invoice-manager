<?php

namespace App\Services;

use App\Models\UserRole;
use App\Repositories\UserRoleRepository;
use App\Repositories\PermissionRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Permission;

class UserRoleService extends BaseService
{
	public function __construct(
		private UserRoleRepository $userRoleRepository,
		private PermissionRepository $permissionRepository,
	) {
		parent::__construct($userRoleRepository);
	}

	/**
	 * Get all user roles.
	 */
	public function getAllUserRoles(): Collection
	{
		return $this->userRoleRepository->getAll();
	}

	/**
	 * Create a new user role.
	 */
	public function createUserRole(array $attributes): UserRole
	{
		$permissions = Arr::pull($attributes, 'permissions');

		$created = $this->userRoleRepository->create([
			...$attributes,
			'created_by' => $this->getAdminAuthUser()->id,
		]);

		return $this->syncPermissions($created->id, $permissions ?? []);
	}

	/**
	 * Get the specified user role.
	 */
	public function getUserRole(int $userId): ?UserRole
	{
		return $this->userRoleRepository->getById($userId);
	}

	/**
	 * Get the specified user role attribute.
	 */
	public function getUserRoleWhere(string $columnName, mixed $value): ?UserRole
	{
		return $this->userRoleRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Delete a specific user role.
	 */
	public function deleteUserRole(int $userId): int
	{
		return $this->userRoleRepository->delete($userId);
	}

	/**
	 * Update an existing user role.
	 */
	public function updateUserRole(int $userId, array $newAttributes): bool
	{
		$permissions = Arr::pull($newAttributes, 'permissions');

		$this->syncPermissions($userId, $permissions ?? []);

		return $this->userRoleRepository->update($userId, [
			...$newAttributes,
			'updated_by' => $this->getAdminAuthUser()->id,
		]);
	}

	/**
	 * Get all permissions.
	 */
	public function getAllPermissions(): Collection
	{
		return $this->permissionRepository->getAll();
	}

	/**
	 * Revoke and assign new permissions to user role.
	 */
	public function syncPermissions(int $userRoleId, array|int|Permission|Collection $permissions): UserRole
	{
		$permissions = Collection::make($permissions)->map(fn($val) => (int)$val);

		return $this->getUserRole($userRoleId)->syncPermissions($permissions);
	}

	/**
	 * Determine if a user role has a certain permission.
	 */
	public function hasPermissionTo(int $userRoleId, string|Permission $permission): bool
	{
		return $this->getUserRole($userRoleId)->hasPermissionTo($permission);
	}
}
