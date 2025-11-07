<?php

use App\Http\Controllers\Admin\RecurringInvoice\RecurringInvoiceController;
use App\RoutePaths\Admin\RecurringInvoice\RecurringInvoiceRoutePath;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Route;

Route::middleware(['verified.admin'])->group(function () {

    Route::controller(RecurringInvoiceController::class)
        ->prefix('recurring-invoices')
        ->group(function () {

            Route::get('/create', 'create')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::CREATE))
                ->name(RecurringInvoiceRoutePath::CREATE);

            Route::post('/', 'store')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::STORE))
                ->name(RecurringInvoiceRoutePath::STORE);

            Route::get('/{recurringInvoice}/show', 'show')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::SHOW))
                ->name(RecurringInvoiceRoutePath::SHOW);

            Route::get('/{recurringInvoice}/edit', 'edit')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::EDIT))
                ->name(RecurringInvoiceRoutePath::EDIT);

            Route::put('/{recurringInvoice}', 'update')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::UPDATE))
                ->name(RecurringInvoiceRoutePath::UPDATE);

            Route::delete('/{recurringInvoice}', 'destroy')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::DESTROY))
                ->name(RecurringInvoiceRoutePath::DESTROY);

            Route::middleware(Authorize::using(RecurringInvoiceRoutePath::INDEX))
                ->group(function () {
                    Route::get('/', 'index')->name(RecurringInvoiceRoutePath::INDEX);
                    Route::post('/list', 'index');
                });

            Route::get('/create/customers', 'customerIndex')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::CUSTOMER_INDEX))
                ->name(RecurringInvoiceRoutePath::CUSTOMER_INDEX);

            Route::post('/create/customers', 'customerStore')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::CUSTOMER_STORE))
                ->name(RecurringInvoiceRoutePath::CUSTOMER_STORE);

            Route::post('/{recurringInvoice}/show/notification', 'customerNotification')
                ->middleware(Authorize::using(RecurringInvoiceRoutePath::CUSTOMER_NOTIFICATION))
                ->name(RecurringInvoiceRoutePath::CUSTOMER_NOTIFICATION);
        });
});
