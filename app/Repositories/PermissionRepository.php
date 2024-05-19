<?php

namespace App\Repositories;

use App\Models\Permission;
use Illuminate\Support\Collection;

class PermissionRepository extends BaseRepository
{
	public function __construct(
		private Permission $permission,
	) {
		parent::__construct($permission);
	}

	/**
	 * Get all permissions.
	 */
	public function getAll(): Collection
	{
		return $this->permission->all();
	}

	/**
	 * Get the specified permission.
	 */
	public function getById(int $permissionId): ?Permission
	{
		return $this->permission->find($permissionId);
	}

	/**
	 * Get the permission by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?Permission
	{
		return $this->permission->where($columnName, $value);
	}

	/**
	 * Get the specified permission.
	 */
	public function getGroupsBy(array|string $columnNames): ?Collection
	{
		return collect($this->permission->all())->groupBy([$columnNames]);
	}

	/**
	 * Delete a specific permission.
	 */
	public function delete(int $permissionId): bool
	{
		return $this->permission->destroy($permissionId);
	}

	/**
	 * Create a new permission.
	 */
	public function create(array $attributes): Permission
	{
		return $this->permission->create($attributes);
	}

	/**
	 * Update an existing permission.
	 */
	public function update(int $permissionId, array $newAttributes): bool
	{
		return $this->permission->whereId($permissionId)
			->update($newAttributes);
	}
}
