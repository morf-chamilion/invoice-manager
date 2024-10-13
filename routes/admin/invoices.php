<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Admin\Invoice\InvoiceRoutePath;
use App\Http\Controllers\Admin\Invoice\InvoiceController;
use Illuminate\Auth\Middleware\Authorize;


Route::middleware(['verified.admin'])->group(function () {

	Route::controller(InvoiceController::class)
		->prefix('invoices')
		->group(function () {

			Route::get('/create', 'create')
				->middleware(Authorize::using(InvoiceRoutePath::CREATE))
				->name(InvoiceRoutePath::CREATE);

			Route::post('/', 'store')
				->middleware(Authorize::using(InvoiceRoutePath::STORE))
				->name(InvoiceRoutePath::STORE);

			Route::get('/{invoice}/show', 'show')
				->middleware(Authorize::using(InvoiceRoutePath::SHOW))
				->name(InvoiceRoutePath::SHOW);

			Route::get('/{invoice}/edit', 'edit')
				->middleware(Authorize::using(InvoiceRoutePath::EDIT))
				->name(InvoiceRoutePath::EDIT);

			Route::get('/{invoice}/download', 'download')
				->middleware(Authorize::using(InvoiceRoutePath::DOWNLOAD))
				->name(InvoiceRoutePath::DOWNLOAD);

			Route::post('/{invoice}/overdue', 'overdue')
				->middleware(Authorize::using(InvoiceRoutePath::OVERDUE))
				->name(InvoiceRoutePath::OVERDUE);

			Route::put('/{invoice}',  'update')
				->middleware(Authorize::using(InvoiceRoutePath::UPDATE))
				->name(InvoiceRoutePath::UPDATE);

			Route::delete('/{invoice}',  'destroy')
				->middleware(Authorize::using(InvoiceRoutePath::DESTROY))
				->name(InvoiceRoutePath::DESTROY);

			Route::middleware(Authorize::using(InvoiceRoutePath::INDEX))
				->group(function () {
					Route::get('/', 'index')->name(InvoiceRoutePath::INDEX);
					Route::post('/list', 'index');
				});

			Route::get('/create/customers', 'customerIndex')
				->middleware(Authorize::using(InvoiceRoutePath::CUSTOMER_INDEX))
				->name(InvoiceRoutePath::CUSTOMER_INDEX);

			Route::post('/create/customers', 'customerStore')
				->middleware(Authorize::using(InvoiceRoutePath::CUSTOMER_STORE))
				->name(InvoiceRoutePath::CUSTOMER_STORE);

			Route::post('/{invoice}/show/notification', 'customerNotification')
				->middleware(Authorize::using(InvoiceRoutePath::CUSTOMER_NOTIFICATION))
				->name(InvoiceRoutePath::CUSTOMER_NOTIFICATION);
		});
});
