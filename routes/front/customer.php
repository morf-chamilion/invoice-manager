<?php

use App\Http\Controllers\Front\Customer\CustomerProfileController;
use App\Http\Controllers\Front\Customer\CustomerInvoiceController;
use App\Http\Controllers\Front\Customer\CustomerController;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.customer', 'verified.customer'])
	->group(function () {

		Route::get('/account/dashboard', [CustomerController::class, 'show'])
			->name(CustomerRoutePath::DASHBOARD_SHOW);

		Route::get('/account/invoices', [CustomerInvoiceController::class, 'index'])
			->name(CustomerRoutePath::INVOICE_INDEX);

		Route::get('/account/invoices/{invoice}/show', [CustomerInvoiceController::class, 'show'])
			->name(CustomerRoutePath::INVOICE_SHOW);

		Route::get('/account/invoices/{invoice}/download', [CustomerInvoiceController::class, 'download'])
			->name(CustomerRoutePath::INVOICE_DOWNLOAD);

		Route::get('/account/edit', [CustomerProfileController::class, 'edit'])
			->name(CustomerRoutePath::EDIT);

		Route::put('/account/edit/{customer}', [CustomerProfileController::class, 'update'])
			->name(CustomerRoutePath::UPDATE);
	});
