<?php

namespace App\Services;

use App\Enums\SettingModule;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use Illuminate\Support\Collection;

class SettingService extends BaseService
{
	/** Module name. */
	public string $module;

	public function __construct(
		private SettingRepository $settingRepository,
	) {
		parent::__construct($settingRepository);
	}

	/**
	 * Get all settings.
	 */
	public function getAllSettings(): Collection
	{
		return $this->settingRepository->getAll();
	}

	/**
	 * Get all settings.
	 */
	public function storeAllSettings(array $attributes): bool
	{
		foreach ($attributes as $key => $value) {
			$success = $this->settingRepository->set($this->module, $key, $value);

			if (!$success) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the setting value for the module with the specified key.
	 */
	public function module(SettingModule|string $module): ?SettingService
	{
		$this->module = $module instanceof SettingModule ? $module->getName() : $module;;

		return $this;
	}

	/**
	 * Get setting value by key.
	 */
	public function get(string $key): mixed
	{
		return $this->settingRepository->getModel()
			->where(['module' => $this->module, 'key' => $key])
			->first()?->value;
	}

	/**
	 * Get media setting by key.
	 */
	public function getMedia(string $key): mixed
	{
		return $this->settingRepository
			->get($this->module, $key)?->getMedia('images');
	}

	/**
	 * Get the first media setting by key.
	 */
	public function getFirstMedia(string $key): mixed
	{
		return $this->settingRepository
			->get($this->module, $key)?->getFirstMedia('images');
	}

	/**
	 * Get the setting value for the module with the specified key.
	 */
	public function set(string $module, string $key): Setting
	{
		return $this->settingRepository->create(['module' => $module, 'key' => $key]);
	}
}
