<?php

namespace App\Http\Controllers\Front\Home;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use App\RoutePaths\Front\Page\PageRoutePath;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HomeController extends FrontBaseController
{
	public function __construct(
		private SettingService $settingService,
	) {
		parent::__construct(settingService: $settingService);
	}

	/**
	 * Show the resource.
	 */
	public function show(): View|RedirectResponse
	{
		$this->sharePageData(SettingModule::HOME);

		return redirect()->route(CustomerRoutePath::INVOICE_INDEX);

		// return view(PageRoutePath::HOME);
	}
}
