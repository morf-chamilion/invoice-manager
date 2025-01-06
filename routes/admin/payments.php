<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Admin\Payment\PaymentRoutePath;
use App\Http\Controllers\Admin\Payment\PaymentController;
use Illuminate\Auth\Middleware\Authorize;


Route::middleware(['verified.admin'])->group(function () {

	Route::controller(PaymentController::class)
		->prefix('payments')
		->group(function () {

			Route::get('/create', 'create')
				->middleware(Authorize::using(PaymentRoutePath::CREATE))
				->name(PaymentRoutePath::CREATE);

			Route::post('/', 'store')
				->middleware(Authorize::using(PaymentRoutePath::STORE))
				->name(PaymentRoutePath::STORE);

			Route::get('/{payment}/show', 'show')
				->middleware(Authorize::using(PaymentRoutePath::SHOW))
				->name(PaymentRoutePath::SHOW);

			Route::get('/{payment}/edit', 'edit')
				->middleware(Authorize::using(PaymentRoutePath::EDIT))
				->name(PaymentRoutePath::EDIT);

			Route::put('/{payment}',  'update')
				->middleware(Authorize::using(PaymentRoutePath::UPDATE))
				->name(PaymentRoutePath::UPDATE);

			Route::delete('/{payment}',  'destroy')
				->middleware(Authorize::using(PaymentRoutePath::DESTROY))
				->name(PaymentRoutePath::DESTROY);

			Route::middleware(Authorize::using(PaymentRoutePath::INDEX))
				->group(function () {
					Route::get('/', 'index')->name(PaymentRoutePath::INDEX);
					Route::post('/list', 'index');
				});
		});
});
