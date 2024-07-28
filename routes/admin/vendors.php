<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Admin\Vendor\VendorRoutePath;
use App\Http\Controllers\Admin\Vendor\VendorController;
use Illuminate\Auth\Middleware\Authorize;

Route::middleware(['verified.admin'])->group(function () {

	Route::controller(VendorController::class)
		->prefix('vendors')
		->group(function () {

			Route::get('/create', 'create')
				->middleware(Authorize::using(VendorRoutePath::CREATE))
				->name(VendorRoutePath::CREATE);

			Route::post('/', 'store')
				->middleware(Authorize::using(VendorRoutePath::STORE))
				->name(VendorRoutePath::STORE);

			Route::get('/{vendor}/edit', 'edit')
				->middleware(Authorize::using(VendorRoutePath::EDIT))
				->name(VendorRoutePath::EDIT);

			Route::put('/{vendor}',  'update')
				->middleware(Authorize::using(VendorRoutePath::UPDATE))
				->name(VendorRoutePath::UPDATE);

			Route::delete('/{vendor}',  'destroy')
				->middleware(Authorize::using(VendorRoutePath::DESTROY))
				->name(VendorRoutePath::DESTROY);

			Route::middleware(Authorize::using(VendorRoutePath::INDEX))
				->group(function () {
					Route::get('/', 'index')->name(VendorRoutePath::INDEX);
					Route::post('/list', 'index');
				});

			Route::get('/invoice-setting-edit', 'edit')
				->middleware(Authorize::using(VendorRoutePath::INVOICE_SETTING_EDIT))
				->name(VendorRoutePath::INVOICE_SETTING_EDIT);
		});
});
