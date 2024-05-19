<?php

use App\Http\Controllers\Front\Checkout\CheckoutFailureController;
use App\Http\Controllers\Front\Checkout\CheckoutController;
use App\Http\Controllers\Front\Checkout\CheckoutSuccessController;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.customer', 'verified.customer'])
	->group(function () {
		Route::match(['GET', 'POST'], '/checkout/session/{id}', [CheckoutController::class, 'show'])
			->name(CheckoutRoutePath::SHOW);

		Route::post('/checkout/create/{id}', [CheckoutController::class, 'create'])
			->name(CheckoutRoutePath::CREATE);

		Route::match(['GET', 'POST'], '/checkout/callback/{id}', [CheckoutController::class, 'store'])
			->name(CheckoutRoutePath::STORE);

		Route::get('/checkout/failure/{id?}', [CheckoutFailureController::class, 'show'])
			->name(CheckoutRoutePath::FAILURE_SHOW);

		Route::get('/checkout/success/{id}', [CheckoutSuccessController::class, 'show'])
			->name(CheckoutRoutePath::SUCCESS_SHOW);

		Route::delete('/checkout/destroy', [CheckoutController::class, 'destroy'])
			->name(CheckoutRoutePath::DESTROY);
	});
