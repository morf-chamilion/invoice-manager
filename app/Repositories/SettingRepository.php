<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Services\MediaService;
use App\Services\Traits\HandlesMedia;
use Illuminate\Support\Collection;

class SettingRepository extends BaseRepository
{
	use HandlesMedia;

	public function __construct(
		private Setting $setting,
		private MediaService $mediaService,
	) {
		parent::__construct($setting);
	}

	/**
	 * Get all settings.
	 */
	public function getAll(): Collection
	{
		return $this->setting->all();
	}

	/**
	 * Get the specified setting.
	 */
	public function getById(int $settingId): ?Setting
	{
		return $this->setting->find($settingId);
	}

	/**
	 * Delete a specific setting.
	 */
	public function delete(int $settingId): bool
	{
		return $this->setting->destroy($settingId);
	}

	/**
	 * Create a new setting.
	 */
	public function create(array $attributes): Setting
	{
		return $this->setting->create($attributes);
	}

	/**
	 * Update an existing setting.
	 */
	public function update(int $settingId, array $newAttributes): bool
	{
		return $this->setting->whereId($settingId)
			->update($newAttributes);
	}

	/**
	 * Set the setting value for the module with the specified key.
	 */
	public function get(string $module, mixed $key): ?Setting
	{
		return $this->setting
			->where(['module' => $module, 'key' => $key])
			->first();
	}

	/**
	 * Set the setting value by module and key.
	 */
	public function set(string $module, mixed $key, mixed $value): mixed
	{
		$setting = $this->setting->updateOrCreate(
			['module' => $module, 'key' => $key],
			['value' => $value]
		);

		$this->syncMedia($setting, 'images', $value);

		return $setting;
	}
}
