<?php

use App\Http\Controllers\Admin\Setting\PageSettingController;
use App\Http\Controllers\Admin\Setting\SettingController;
use App\Http\Controllers\Admin\Setting\StoreSettingController;
use App\RoutePaths\Admin\Setting\PageSettingRoutePath;
use App\RoutePaths\Admin\Setting\SettingRoutePath;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Route;

Route::post('/settings', StoreSettingController::class)
	->name(SettingRoutePath::STORE);

Route::middleware(['verified.admin'])->group(function () {

	Route::controller(SettingController::class)
		->prefix('settings')
		->group(function () {

			Route::get('/general', 'general')
				->middleware(Authorize::using(SettingRoutePath::GENERAL))
				->name(SettingRoutePath::GENERAL);

			Route::get('/mail', 'mail')
				->middleware(Authorize::using(SettingRoutePath::MAIL))
				->name(SettingRoutePath::MAIL);

			Route::get('/quotation', 'quotation')
				->middleware(Authorize::using(SettingRoutePath::QUOTATION))
				->name(SettingRoutePath::QUOTATION);

			Route::get('/invoice', 'invoice')
				->middleware(Authorize::using(SettingRoutePath::INVOICE))
				->name(SettingRoutePath::INVOICE);
		});

	Route::controller(PageSettingController::class)
		->prefix('pages')
		->group(function () {

			Route::get('/home', 'home')
				->middleware(Authorize::using(PageSettingRoutePath::HOME))
				->name(PageSettingRoutePath::HOME);
		});
});
