<?php

namespace App\Repositories;

use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
	public function __construct(
		private User $user,
	) {
		parent::__construct($user);
	}

	/**
	 * Get all users.
	 */
	public function getAll(): Collection
	{
		return $this->user::all();
	}

	/**
	 * Get the specified user.
	 */
	public function getById(int $userId): ?User
	{
		return $this->user::find($userId);
	}

	/**
	 * Get the user by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?User
	{
		return $this->user::where($columnName, $value)->first();
	}

	/**
	 * Delete a specific user.
	 */
	public function delete(int $userId): bool
	{
		// The super admin user is based on the table row id.
		// Deleting super admin can cause side effects.
		if ($userId === AuthServiceProvider::SUPER_ADMIN) {
			return false;
		}

		return $this->user::destroy($userId);
	}

	/**
	 * Create a new user.
	 */
	public function create(array $attributes): User
	{
		return $this->user::create([
			...$attributes,
			'password' => Hash::make($attributes['password'])
		]);
	}

	/**
	 * Update an existing user.
	 */
	public function update(int $userId, array $newAttributes): bool
	{
		$password = Arr::pull($newAttributes, 'password');

		if ($password) {
			$newAttributes['password'] = Hash::make($password);
		}

		return $this->user::whereId($userId)
			->update($newAttributes);
	}
}
