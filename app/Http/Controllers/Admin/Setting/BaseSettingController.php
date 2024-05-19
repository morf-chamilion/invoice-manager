<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Admin\AdminBaseController;
use App\RoutePaths\Admin\Setting\SettingRoutePath;
use Illuminate\Support\Facades\App;

class BaseSettingController extends AdminBaseController
{
	public string $sessionKey = 'module';

	/**
	 * Get the url for the session store route.
	 */
	public function settingStoreRoute(): string
	{
		return route($this->settingRoutePath()::STORE);
	}

	/**
	 * Setting route paths.
	 */
	protected function settingRoutePath(): SettingRoutePath
	{
		$settingRoutePath = App::make(SettingRoutePath::class);

		return $settingRoutePath;
	}
}
