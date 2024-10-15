<?php

namespace App\Http\Controllers\Front\Invoice;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\RoutePaths\Front\Invoice\InvoiceRoutePath;
use App\RoutePaths\Front\Page\PageRoutePath;
use App\Services\CustomerService;
use App\Services\InvoiceService;
use App\Services\SettingService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;

class InvoiceController extends FrontBaseController
{
	public function __construct(
		private SettingService $settingService,
		private CustomerService $customerService,
		private InvoiceService $invoiceService,
	) {
		parent::__construct(settingService: $settingService);
	}
	/**
	 * Show the resource.
	 */
	public function show(string $id): View|RedirectResponse
	{
		try {
			$invoiceId = (int) Crypt::decryptString($id);
		} catch (DecryptException) {
			return redirect()->route(PageRoutePath::HOME)
				->withErrors('Invoice ID is invalid.');
		}
		$invoice = $this->invoiceService->getInvoice($invoiceId);
		$this->sharePageData(SettingModule::GENERAL);
		if (!$invoice) {
			return view(InvoiceRoutePath::SHOW);
		}
		return view(InvoiceRoutePath::SHOW)->with([
			'invoice' => $invoice,
		]);
	}
}
