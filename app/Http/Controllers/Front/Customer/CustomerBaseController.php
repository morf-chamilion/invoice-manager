<?php

namespace App\Http\Controllers\Front\Customer;

use App\Enums\SettingModule;
use App\Http\Controllers\Controller;
use App\Http\Traits\JsonResponseTrait;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use App\Services\CustomerService;
use App\Services\SettingService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\App;

class CustomerBaseController extends Controller
{
	use JsonResponseTrait;

	public function __construct(
		private SettingService $settingService,
	) {
	}

	/**
	 * Get the customer service instance.
	 */
	protected function getService(): CustomerService
	{
		return App::make(CustomerService::class);
	}

	/**
	 * Get the service instance.
	 */
	protected function settingService(): SettingService
	{
		return $this->settingService();
	}

	/**
	 * Share specific data to views.
	 */
	public function sharePageData(SettingModule|string $settingModule): CustomerBaseController
	{
		$pageData = $this->settingService->module($settingModule);

		$this->pageIdentifier();

		View::share('pageData', $pageData);

		return $this;
	}

	/**
	 * Inject HTML body class to identify the route.
	 */
	public static function pageIdentifier(?string $bodyClass = null): void
	{
		$classNames = Str::slug($bodyClass) ?? Str::replace('.', '-', Request::route()->getName());

		addHtmlClass('body', $classNames);
	}

	/**
	 * Register breadcrumb for the current route.
	 */
	protected function registerBreadcrumb(
		string $routeTitle,
		string $parentRouteName = CustomerRoutePath::DASHBOARD_SHOW,
		string $parentRouteTitle = '',
		string $currentRouteName = null,
		string $routeParameter = null,
	): void {
		if (!$currentRouteName) {
			$currentRouteName = Route::currentRouteName();
		}

		if ($parentRouteName !== CustomerRoutePath::DASHBOARD_SHOW) {
			Breadcrumbs::for($parentRouteName, function (BreadcrumbTrail $trail)
			use ($parentRouteName, $parentRouteTitle) {
				$trail->parent(CustomerRoutePath::DASHBOARD_SHOW);
				$trail->push($parentRouteTitle, route($parentRouteName));
			});
		}

		if ($currentRouteName !== CustomerRoutePath::DASHBOARD_SHOW) {
			Breadcrumbs::for(CustomerRoutePath::DASHBOARD_SHOW, function (BreadcrumbTrail $trail)
			use ($routeTitle) {
				$trail->push($routeTitle, route(CustomerRoutePath::DASHBOARD_SHOW));
			});
		}

		Breadcrumbs::for($currentRouteName, function (BreadcrumbTrail $trail)
		use ($currentRouteName, $parentRouteName, $routeParameter, $routeTitle) {
			if ($parentRouteName !== $currentRouteName) {
				$trail->parent($parentRouteName);
			}

			$trail->push($routeTitle, route($currentRouteName, $routeParameter));
		});
	}
}
