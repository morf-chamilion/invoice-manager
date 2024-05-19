<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Admin\User\UserRoutePath;
use App\Http\Controllers\Admin\User\UserController;
use Illuminate\Auth\Middleware\Authorize;

Route::middleware(['verified.admin'])->group(function () {

	Route::controller(UserController::class)
		->prefix('users')
		->group(function () {

			Route::get('/create', 'create')
				->middleware(Authorize::using(UserRoutePath::CREATE))
				->name(UserRoutePath::CREATE);

			Route::post('/', 'store')
				->middleware(Authorize::using(UserRoutePath::STORE))
				->name(UserRoutePath::STORE);

			Route::get('/{user}/edit', 'edit')
				->middleware(Authorize::using(UserRoutePath::EDIT))
				->name(UserRoutePath::EDIT);

			Route::put('/{user}',  'update')
				->middleware(Authorize::using(UserRoutePath::UPDATE))
				->name(UserRoutePath::UPDATE);

			Route::delete('/{user}',  'destroy')
				->middleware(Authorize::using(UserRoutePath::DESTROY))
				->name(UserRoutePath::DESTROY);

			Route::middleware(Authorize::using(UserRoutePath::INDEX))
				->group(function () {
					Route::get('/', 'index')->name(UserRoutePath::INDEX);
					Route::post('/list', 'index');
				});
		});
});
