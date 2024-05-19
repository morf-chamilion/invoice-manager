<?php

namespace App\Http\Controllers\Front\Customer;

use App\Enums\SettingModule;
use App\Exceptions\RedirectResponseException;
use App\Http\Requests\Front\Customer\CustomerUpdateRequest;
use App\Messages\CustomerMessage;
use App\Models\Customer;
use App\Providers\CustomerServiceProvider;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use App\Services\CustomerService;
use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;

class CustomerProfileController extends CustomerBaseController
{
	public function __construct(
		private SettingService $settingService,
		private CustomerService $customerService,
		private CustomerMessage $customerMessage,
	) {
		parent::__construct(settingService: $settingService);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(): ViewContract
	{
		$this->sharePageData(SettingModule::HOME);

		$this->registerBreadcrumb(
			routeTitle: __('Profile'),
		);

		View::share('title', __('Profile Settings'));

		return view(CustomerRoutePath::EDIT)->with([
			'customer' => CustomerServiceProvider::getAuthUser(),
			'title' => __('Profile Settings'),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Customer $customer, CustomerUpdateRequest $request): RedirectResponse|RedirectResponseException
	{
		$updated = $this->customerService->updateCustomer($customer->id, $request->getAttributes());

		throw_if(!$updated, RedirectResponseException::class, $this->customerMessage->updateFailed());

		return redirect()->route(CustomerRoutePath::EDIT)->with([
			'message' => $this->customerMessage->updateSuccess(),
			'status' => true,
		]);
	}
}
