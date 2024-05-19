<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\RoutePaths\Admin\AdminRoutePath;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(AdminRoutePath::DASHBOARD));

Route::middleware(['auth.admin', 'verified.admin'])
	->group(function () {

		Route::get('/dashboard', [DashboardController::class, 'index'])
			->name(AdminRoutePath::DASHBOARD);

		Route::get('/profile', [ProfileController::class, 'edit'])
			->name(AdminRoutePath::PROFILE_EDIT);

		Route::patch('/profile', [ProfileController::class, 'update'])
			->name(AdminRoutePath::PROFILE_UPDATE);

		Route::delete('/profile', [ProfileController::class, 'destroy'])
			->name(AdminRoutePath::PROFILE_DESTROY);
	});
