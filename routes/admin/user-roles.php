<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Admin\User\UserRoleRoutePath;
use App\Http\Controllers\Admin\User\UserRoleController;
use Illuminate\Auth\Middleware\Authorize;

Route::middleware(['verified.admin'])->group(function () {

	Route::controller(UserRoleController::class)
		->prefix('user-roles')
		->group(function () {

			Route::get('/create', 'create')
				->middleware(Authorize::using(UserRoleRoutePath::CREATE))
				->name(UserRoleRoutePath::CREATE);

			Route::post('/', 'store')
				->middleware(Authorize::using(UserRoleRoutePath::STORE))
				->name(UserRoleRoutePath::STORE);

			Route::get('/{userRole}/edit', 'edit')
				->middleware(Authorize::using(UserRoleRoutePath::EDIT))
				->name(UserRoleRoutePath::EDIT);

			Route::put('/{userRole}',  'update')
				->middleware(Authorize::using(UserRoleRoutePath::UPDATE))
				->name(UserRoleRoutePath::UPDATE);

			Route::delete('/{userRole}',  'destroy')
				->middleware(Authorize::using(UserRoleRoutePath::DESTROY))
				->name(UserRoleRoutePath::DESTROY);

			Route::middleware(Authorize::using(UserRoleRoutePath::INDEX))
				->group(function () {
					Route::get('/', 'index')->name(UserRoleRoutePath::INDEX);
					Route::post('/list', 'index');
				});
		});
});
