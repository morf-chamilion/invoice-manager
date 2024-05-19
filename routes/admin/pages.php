<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Admin\Page\PageRoutePath;
use App\Http\Controllers\Admin\Page\PageController;
use Illuminate\Auth\Middleware\Authorize;

Route::middleware(['verified.admin'])->group(function () {

	Route::controller(PageController::class)
		->prefix('content-pages')
		->group(function () {

			Route::get('/create', 'create')
				->middleware(Authorize::using(PageRoutePath::CREATE))
				->name(PageRoutePath::CREATE);

			Route::post('/', 'store')
				->middleware(Authorize::using(PageRoutePath::STORE))
				->name(PageRoutePath::STORE);

			Route::get('/{page}/edit', 'edit')
				->middleware(Authorize::using(PageRoutePath::EDIT))
				->name(PageRoutePath::EDIT);

			Route::put('/{page}',  'update')
				->middleware(Authorize::using(PageRoutePath::UPDATE))
				->name(PageRoutePath::UPDATE);

			Route::delete('/{page}',  'destroy')
				->middleware(Authorize::using(PageRoutePath::DESTROY))
				->name(PageRoutePath::DESTROY);

			Route::middleware(Authorize::using(PageRoutePath::INDEX))
				->group(function () {
					Route::get('/', 'index')->name(PageRoutePath::INDEX);
					Route::post('/list', 'index');
				});
		});
});
