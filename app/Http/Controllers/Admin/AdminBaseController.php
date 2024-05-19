<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\JsonResponseTrait;
use App\RoutePaths\Admin\AdminRoutePath;
use App\Services\BaseService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class AdminBaseController extends Controller implements AdminBaseControllerInterface
{
	use JsonResponseTrait;

	public function __construct(
		protected BaseService $service,
	) {
	}

	/**
	 * Get the service instance.
	 */
	protected function getService(): BaseService
	{
		// Hint!, if you get an initialization error which means you should call
		// the parent constructor when extending from this base controller.
		return $this->service;
	}

	/**
	 * Get the setting service instance.
	 */
	private function getSettingService(): SettingService
	{
		return App::make(SettingService::class);
	}

	/**
	 * Share specific data to views.
	 */
	public function sharePageData(array $pageData): void
	{
		View::share('pageData', $pageData);
	}

	/**
	 * Get resource title based on the current route action.
	 */
	public function getActionTitle(string $action = null): string
	{
		$modelName = $this->getService()->modelName();
		$resourcePlural = Str::of($modelName)->plural()->title();
		$resourceSingular = Str::of($modelName)->singular()->title();

		if (!$action) {
			$action = $this->getRouteAction();
		}

		return match ($action) {
			'index' => "List {$resourcePlural}",
			'create' => "Create {$resourceSingular}",
			'edit' => "Edit {$resourceSingular}",
			default => Str::of("{$action} {$resourceSingular}")->title()->plural()->replace('-', ' '),
		};
	}

	/**
	 * Get the current route action.
	 */
	private function getRouteAction(): string
	{
		$action = Str::afterLast(Route::currentRouteName(), '.');

		return match ($action) {
			'index', 'create', 'update', 'store', 'edit' => $action,
			default => Arr::last(Request::segments()),
		};
	}

	/**
	 * Set the data table columns.
	 */
	public function tableColumns(array $columns)
	{
		return array_merge(['id'], $columns, ['status', 'actions']);
	}

	/**
	 * Register breadcrumb for the current route.
	 */
	protected function registerBreadcrumb(
		string $parentRouteName = AdminRoutePath::DASHBOARD,
		string $currentRouteName = null,
		string $routeParameter = null,
		string $routeTitle = null,
	): void {
		if (!$currentRouteName) {
			$currentRouteName = Route::currentRouteName();
		}

		if (!$routeTitle) {
			$routeTitle = $this->getActionTitle();
		}

		if ($parentRouteName !== AdminRoutePath::DASHBOARD) {
			Breadcrumbs::for($parentRouteName, function (BreadcrumbTrail $trail)
			use ($parentRouteName) {
				$trail->parent(AdminRoutePath::DASHBOARD);
				$trail->push($this->getActionTitle('index'), route($parentRouteName));
			});
		}

		if ($currentRouteName !== AdminRoutePath::DASHBOARD) {
			Breadcrumbs::for(AdminRoutePath::DASHBOARD, function (BreadcrumbTrail $trail) {
				$trail->push($this->getActionTitle('index'), route(AdminRoutePath::DASHBOARD));
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
