<?php

namespace App\Http\Controllers\Front\Customer;

use App\Enums\SettingModule;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use App\Services\CustomerService;
use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;

class CustomerController extends CustomerBaseController
{
	public function __construct(
		private SettingService $settingService,
		private CustomerService $customerService,
	) {
		parent::__construct(settingService: $settingService);
	}

	/**
	 * Show the resource.
	 */
	public function show(): ViewContract|RedirectResponse
	{
		$this->sharePageData(SettingModule::HOME);

		$this->registerBreadcrumb(
			routeTitle: __('Dashboard'),
		);

		View::share('title', __('Dashboard'));

		return redirect()->route(CustomerRoutePath::INVOICE_INDEX);

		// return view(CustomerRoutePath::DASHBOARD_SHOW)->with([
		// 	'customer' => CustomerServiceProvider::getAuthUser(),
		// ]);
	}
}
