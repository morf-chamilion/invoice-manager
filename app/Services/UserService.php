<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\Admin\AdminCreateNotification;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use Exception;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Role;
use Illuminate\Support\Arr;

class UserService extends BaseService
{
	public function __construct(
		private UserRepository $userRepository,
		private UserRoleRepository $userRoleRepository,
		private SettingService $settingService,
		private VendorService $vendorService,
	) {
		parent::__construct($userRepository);
	}

	/**
	 * Get all users.
	 */
	public function getAllUsers(): Collection
	{
		return $this->userRepository->getAll();
	}

	/**
	 * Create a new user.
	 */
	public function createUser(array $attributes): User
	{
		$role = Arr::pull($attributes, 'role');
		$notification = Arr::pull($attributes, 'notification');

		$user = $this->userRepository->create([
			...$attributes,
			'created_by' => $this->getAdminAuthUser()->id,
		]);

		$this->markEmailVerified($user, $attributes['status']);

		if ($notification) {
			$this->userCreateNotification($user, $attributes['password']);
		}

		return $this->syncRoles($user->id, $role ?? []);
	}

	/**
	 * Get the specified user.
	 */
	public function getUser(int $userId): ?User
	{
		return $this->userRepository->getById($userId);
	}

	/**
	 * Get the specified user attribute.
	 */
	public function getUserWhere(string $columnName, mixed $value): ?User
	{
		return $this->userRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Delete a specific user.
	 */
	public function deleteUser(int $userId): int
	{
		return $this->userRepository->delete($userId);
	}

	/**
	 * Update an existing user.
	 */
	public function updateUser(int $userId, array $newAttributes): bool
	{
		$role = Arr::pull($newAttributes, 'role');

		$this->syncRoles($userId, $role ?? []);

		$updated = $this->userRepository->update($userId, [
			...$newAttributes,
			'updated_by' => $this->getAdminAuthUser()->id,
		]);

		$user = $this->getUser($userId);

		$this->markEmailVerified($user, $newAttributes['status']);

		return $updated;
	}

	/**
	 * Check if the user mail is verified.
	 */
	public function isUserMailVerified(int $userId): bool
	{
		$user = $this->userRepository->getById($userId);

		return (bool) $user->email_verified_at;
	}

	/**
	 * Get all roles.
	 */
	public function getAllRoles(): Collection
	{
		return $this->userRoleRepository->getAll();
	}

	/**
	 * Revoke and assign new roles to user.
	 */
	public function syncRoles(int $userId, string|array|int|Role|Collection $roles): User
	{
		$roles = Collection::make($roles)->map(fn($val) => (int)$val);

		return $this->getUser($userId)->syncRoles($roles);
	}

	/**
	 * Determine if a user has a certain role.
	 */
	public function hasRole(int $userId, string|int|Role|Collection $roles): bool
	{
		return $this->getUser($userId)->hasRole($roles);
	}

	/**
	 * Mark the given user's email as verified.
	 */
	private function markEmailVerified(User $user, $status): bool
	{
		if ($this->getAdminAuthUser() && $status == UserStatus::ACTIVE->value) {
			$user->markEmailAsVerified();
		}

		return false;
	}

	/**
	 * Handle user create notfications.
	 */
	private function userCreateNotification(
		User $user,
		#[\SensitiveParameter]
		string $userPasswordPlainText,
	): bool|Exception {
		try {
			$user->notify(
				new AdminCreateNotification($user, $userPasswordPlainText),
			);

			return true;
		} catch (Exception $e) {
			return $e;
		}

		return false;
	}

	/**
	 * Get all vendors.
	 */
	public function getAllVendors(): Collection
	{
		return $this->vendorService->getAllVendors();
	}
}
