<?php

namespace App\Http\Controllers\Front\Checkout;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;
use App\Services\InvoiceService;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;

class CheckoutFailureController extends FrontBaseController
{
	public function __construct(
		private SettingService $settingService,
		private InvoiceService $invoiceService,
	) {
		parent::__construct(settingService: $settingService);
	}

	/**
	 * Show checkout failture view.
	 */
	public function show(?string $id = null): View
	{
		$this->sharePageData(SettingModule::GENERAL);

		return view(CheckoutRoutePath::FAILURE_SHOW)->with([
			'sessionId' => $id,
		]);
	}
}
