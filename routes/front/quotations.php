<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Front\Quotation\QuotationRoutePath;
use App\Http\Controllers\Front\Quotation\QuotationController;

Route::get('/quotations/{id}/show',  [QuotationController::class, 'show'])
	->name(QuotationRoutePath::SHOW);
