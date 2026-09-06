<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The currency code shown alongside every formatted amount in the app, and
    | submitted with each checkout session. Read by App\Handlers\MoneyHandler.
    |
    */

    'currency_code' => env('CHECKOUT_CURRENCY_CODE', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | CyberSource Gateway
    |--------------------------------------------------------------------------
    |
    | Credentials for the hosted CyberSource Secure Acceptance profile. The
    | secret key signs the request payload and must never reach the browser.
    |
    */

    'gateway' => [
        'url'         => env('CHECKOUT_GATEWAY_URL'),
        'merchant_id' => env('CHECKOUT_MERCHANT_ID'),
        'access_key'  => env('CHECKOUT_ACCESS_KEY'),
        'secret_key'  => env('CHECKOUT_SECRET_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing Defaults
    |--------------------------------------------------------------------------
    |
    | Fallbacks for the unsigned billing fields CyberSource requires but which
    | the customer record does not always carry.
    |
    */

    'billing' => [
        'address_city' => env('CHECKOUT_BILL_TO_ADDRESS_CITY', 'colombo'),
    ],

];
