<?php

use Illuminate\Support\Facades\Route;
use App\RoutePaths\Front\Invoice\InvoiceRoutePath;
use App\Http\Controllers\Front\Invoice\InvoiceController;

Route::get('/invoices/{id}/show',  [InvoiceController::class, 'show'])
	->name(InvoiceRoutePath::SHOW);
