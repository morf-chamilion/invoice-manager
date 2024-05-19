<?php

namespace App\Http\Controllers\Front\Customer;

use App\Enums\SettingModule;
use App\Models\Invoice;
use App\Providers\CustomerServiceProvider;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use App\Services\CustomerService;
use App\Services\InvoiceService;
use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\Response;

class CustomerInvoiceController extends CustomerBaseController
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
	public function index(): ViewContract
	{
		$this->sharePageData(SettingModule::HOME);

		$this->registerBreadcrumb(
			routeTitle: __('Invoices'),
		);

		View::share('title', __('All Invoices'));

		$customer = CustomerServiceProvider::getAuthUser();

		return view(CustomerRoutePath::INVOICE_INDEX)->with([
			'invoices' => $this->invoiceService->getCustomerInvoices($customer->id),
		]);
	}

	/**
	 * Show the resource.
	 */
	public function show(Invoice $invoice): ViewContract
	{
		$this->sharePageData(SettingModule::HOME);

		$this->registerBreadcrumb(
			parentRouteName: CustomerRoutePath::INVOICE_INDEX,
			parentRouteTitle: __('Invoices'),
			routeParameter: $invoice->customer->id,
			routeTitle: __('View Invoice'),
		);

		View::share('title', __('Invoice :number', ['number' => $invoice->number]));

		$customer = CustomerServiceProvider::getAuthUser();
		$invoice = $this->invoiceService->getCustomerInvoice($invoice->id, $customer->id);

		if (!$invoice) {
			return view(CustomerRoutePath::INVOICE_SHOW)->with([
				'customer' => $customer,
			]);
		}

		return view(CustomerRoutePath::INVOICE_SHOW)->with([
			'customer' => $customer,
			'invoice' => $invoice,
		]);
	}

	/**
	 * Download the specified resource view.
	 */
	public function download(Invoice $invoice): Response
	{
		$fileName = $this->invoiceService->invoiceFileName($invoice);
		$view = $this->invoiceService->invoicePDF($invoice);

		return $view->download("{$fileName}.pdf");
	}
}
