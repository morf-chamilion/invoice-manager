<?php

namespace App\Http\Controllers\Front;

use App\Enums\SettingModule;
use App\Http\Controllers\Controller;
use App\Http\Traits\JsonResponseTrait;
use App\Services\SettingService;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class FrontBaseController extends Controller
{
	use JsonResponseTrait;

	public function __construct(
		private SettingService $settingService,
	) {
	}

	/**
	 * Get the service instance.
	 */
	protected function settingService(): SettingService
	{
		// Hint!, if you get an initialization error which means you should call
		// the parent constructor when extending from this base controller.
		return $this->settingService();
	}

	/**
	 * Share specific data to views.
	 */
	public function sharePageData(SettingModule|string $settingModule): FrontBaseController
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
}
