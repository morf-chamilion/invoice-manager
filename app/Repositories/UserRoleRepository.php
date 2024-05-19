<?php

namespace App\Repositories;

use App\Models\UserRole;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class UserRoleRepository extends BaseRepository
{
	public function __construct(
		private UserRole $userRole,
	) {
		parent::__construct($userRole);
	}

	/**
	 * Get all user roles.
	 */
	public function getAll(): Collection
	{
		return $this->userRole::all();
	}

	/**
	 * Get the specified user role.
	 */
	public function getById(int $userRoleId): ?UserRole
	{
		return $this->userRole::find($userRoleId);
	}

	/**
	 * Get the user role by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?UserRole
	{
		return $this->userRole::where($columnName, $value)->first();
	}

	/**
	 * Delete a specific use role.
	 */
	public function delete(int $userRoleId): bool
	{
		$userRole = $this->getById($userRoleId);

		$this->checkModelHasParentRelations($userRole);

		try {
			return $userRole->delete($userRoleId);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage());

			return false;
		}
	}

	/**
	 * Create a new user role.
	 */
	public function create(array $attributes): UserRole
	{
		return $this->userRole::create($attributes);
	}

	/**
	 * Update an existing user role.
	 */
	public function update(int $userId, array $newAttributes): bool
	{
		return $this->userRole::whereId($userId)
			->update($newAttributes);
	}
}
