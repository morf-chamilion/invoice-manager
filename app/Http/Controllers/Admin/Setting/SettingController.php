<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Enums\SettingModule;
use App\Messages\SettingMessage;
use App\RoutePaths\Admin\Setting\SettingRoutePath;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;

class SettingController extends BaseSettingController
{
	public function __construct(
		private SettingService $settingService,
		private SettingRoutePath $settingRoutePath,
		private SettingMessage $settingMessage,
	) {
		parent::__construct(service: $settingService);
	}

	/**
	 * Show the form for updating the resource.
	 */
	public function general(): View
	{
		$module = SettingModule::GENERAL;
		$resourceTitle = $this->getActionTitle();

		$this->registerBreadcrumb(routeTitle: $resourceTitle);

		Session::put('module', $module);

		$this->sharePageData([
			'title' => $resourceTitle,
		]);

		return view($this->settingRoutePath::GENERAL, [
			'settings' => $this->settingService->module($module),
			'action' => $this->settingStoreRoute(),
		]);
	}

	/**
	 * Show the form for updating the resource.
	 */
	public function mail(): View
	{
		$module = SettingModule::MAIL;
		$resourceTitle = $this->getActionTitle();

		$this->registerBreadcrumb(routeTitle: $resourceTitle);

		Session::put('module', $module);

		$this->sharePageData([
			'title' => $resourceTitle,
		]);

		return view($this->settingRoutePath::MAIL, [
			'settings' => $this->settingService->module($module),
			'action' => $this->settingStoreRoute(),
		]);
	}

	/**
	 * Show the form for updating the resource.
	 */
	public function quotation(): View
	{
		$module = SettingModule::QUOTATION;
		$resourceTitle = $this->getActionTitle();

		$this->registerBreadcrumb(routeTitle: $resourceTitle);

		Session::put('module', $module);

		$this->sharePageData([
			'title' => $resourceTitle,
		]);

		return view($this->settingRoutePath::QUOTATION, [
			'settings' => $this->settingService->module($module),
			'action' => $this->settingStoreRoute(),
		]);
	}

	/**
	 * Show the form for updating the resource.
	 */
	public function invoice(): View
	{
		$module = SettingModule::INVOICE;
		$resourceTitle = $this->getActionTitle();

		$this->registerBreadcrumb(routeTitle: $resourceTitle);

		Session::put('module', $module);

		$this->sharePageData([
			'title' => $resourceTitle,
		]);

		return view($this->settingRoutePath::INVOICE, [
			'settings' => $this->settingService->module($module),
			'action' => $this->settingStoreRoute(),
		]);
	}
}
