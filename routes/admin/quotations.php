<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Admin\Quotation\QuotationRoutePath;
use App\Http\Controllers\Admin\Quotation\QuotationController;
use Illuminate\Auth\Middleware\Authorize;


Route::middleware(['verified.admin'])->group(function () {

	Route::controller(QuotationController::class)
		->prefix('quotations')
		->group(function () {

			Route::get('/create', 'create')
				->middleware(Authorize::using(QuotationRoutePath::CREATE))
				->name(QuotationRoutePath::CREATE);

			Route::post('/', 'store')
				->middleware(Authorize::using(QuotationRoutePath::STORE))
				->name(QuotationRoutePath::STORE);

			Route::get('/{quotation}/show', 'show')
				->middleware(Authorize::using(QuotationRoutePath::SHOW))
				->name(QuotationRoutePath::SHOW);

			Route::get('/{quotation}/edit', 'edit')
				->middleware(Authorize::using(QuotationRoutePath::EDIT))
				->name(QuotationRoutePath::EDIT);

			Route::get('/{quotation}/download', 'download')
				->middleware(Authorize::using(QuotationRoutePath::DOWNLOAD))
				->name(QuotationRoutePath::DOWNLOAD);

			Route::put('/{quotation}',  'update')
				->middleware(Authorize::using(QuotationRoutePath::UPDATE))
				->name(QuotationRoutePath::UPDATE);

			Route::delete('/{quotation}',  'destroy')
				->middleware(Authorize::using(QuotationRoutePath::DESTROY))
				->name(QuotationRoutePath::DESTROY);

			Route::middleware(Authorize::using(QuotationRoutePath::INDEX))
				->group(function () {
					Route::get('/', 'index')->name(QuotationRoutePath::INDEX);
					Route::post('/list', 'index');
				});

			Route::get('/create/customers', 'customerIndex')
				->middleware(Authorize::using(QuotationRoutePath::CUSTOMER_INDEX))
				->name(QuotationRoutePath::CUSTOMER_INDEX);

			Route::post('/create/customers', 'customerStore')
				->middleware(Authorize::using(QuotationRoutePath::CUSTOMER_STORE))
				->name(QuotationRoutePath::CUSTOMER_STORE);

			Route::post('/{quotation}/show/notification', 'customerNotification')
				->middleware(Authorize::using(QuotationRoutePath::CUSTOMER_NOTIFICATION))
				->name(QuotationRoutePath::CUSTOMER_NOTIFICATION);
		});
});
