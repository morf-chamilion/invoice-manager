<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The currency code shown alongside every formatted amount in the app.
    | Read by App\Handlers\MoneyHandler.
    |
    */

    'currency_code' => env('CHECKOUT_CURRENCY_CODE', 'USD'),

];
