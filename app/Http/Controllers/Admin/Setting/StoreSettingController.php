<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Exceptions\RedirectResponseException;
use App\Messages\SettingMessage;
use App\RoutePaths\Admin\Setting\SettingRoutePath;
use App\Services\PermissionService;
use App\Services\SettingService;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreSettingController extends BaseSettingController
{
	public function __construct(
		private SettingService $settingService,
		private SettingRoutePath $settingRoutePath,
		private SettingMessage $settingMessage,
		private PermissionService $permissionService,
	) {
		parent::__construct(service: $settingService);

		$allPermissions = $this->permissionService
			->getAllPermissions()
			->pluck('name')
			->implode(',');

		// Good for now as long as we trust admins.
		$this->middleware(Authorize::using($allPermissions));
	}

	/**
	 * Store or update resource in storage.
	 *
	 * This method requires a module name to be passed via
	 * session and needs to be named as the sessionKey.
	 */
	public function __invoke(Request $request): RedirectResponse|RedirectResponseException
	{
		$moduleName = Session::remove($this->sessionKey);

		throw_if(!$moduleName, RedirectResponseException::class, $this->settingMessage->createFailed());

		$attributes = $request->except(['_token', '_method']);

		$updated = $this->settingService->module($moduleName)
			->storeAllSettings($attributes);

		throw_if(!$updated, RedirectResponseException::class, $this->settingMessage->createFailed());

		return redirect()->back()->with([
			'message' => $this->settingMessage->updateSuccess(),
			'status' => true,
		]);
	}
}
