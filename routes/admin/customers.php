<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Admin\Customer\CustomerRoutePath;
use App\Http\Controllers\Admin\Customer\CustomerController;
use Illuminate\Auth\Middleware\Authorize;

Route::middleware(['verified.admin'])->group(function () {

	Route::controller(CustomerController::class)
		->prefix('customers')
		->group(function () {

			Route::get('/create', 'create')
				->middleware(Authorize::using(CustomerRoutePath::CREATE))
				->name(CustomerRoutePath::CREATE);

			Route::post('/', 'store')
				->middleware(Authorize::using(CustomerRoutePath::STORE))
				->name(CustomerRoutePath::STORE);

			Route::get('/{customer}/edit', 'edit')
				->middleware(Authorize::using(CustomerRoutePath::EDIT))
				->name(CustomerRoutePath::EDIT);

			Route::put('/{customer}',  'update')
				->middleware(Authorize::using(CustomerRoutePath::UPDATE))
				->name(CustomerRoutePath::UPDATE);

			Route::delete('/{customer}',  'destroy')
				->middleware(Authorize::using(CustomerRoutePath::DESTROY))
				->name(CustomerRoutePath::DESTROY);

			Route::middleware(Authorize::using(CustomerRoutePath::INDEX))
				->group(function () {
					Route::get('/', 'index')->name(CustomerRoutePath::INDEX);
					Route::post('/list', 'index');
				});
		});
});
