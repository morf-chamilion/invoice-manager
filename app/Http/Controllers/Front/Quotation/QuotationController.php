<?php

namespace App\Http\Controllers\Front\Quotation;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\RoutePaths\Front\Quotation\QuotationRoutePath;
use App\RoutePaths\Front\Page\PageRoutePath;
use App\Services\CustomerService;
use App\Services\QuotationService;
use App\Services\SettingService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;

class QuotationController extends FrontBaseController
{
	public function __construct(
		private SettingService $settingService,
		private CustomerService $customerService,
		private QuotationService $quotationService,
	) {
		parent::__construct(settingService: $settingService);
	}
	/**
	 * Show the resource.
	 */
	public function show(string $id): View|RedirectResponse
	{
		try {
			$quotationId = (int) Crypt::decryptString($id);
		} catch (DecryptException) {
			return redirect()->route(PageRoutePath::HOME)
				->withErrors('Quotation ID is invalid.');
		}

		$quotation = $this->quotationService->getQuotation($quotationId);

		$this->sharePageData(SettingModule::GENERAL);

		if (!$quotation) {
			return view(QuotationRoutePath::SHOW);
		}

		return view(QuotationRoutePath::SHOW)->with([
			'quotation' => $quotation,
		]);
	}
}
