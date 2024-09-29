<?php

namespace App\Handlers;

use Illuminate\Support\Env;

abstract class MoneyHandler
{
	/**
	 * Get the currency code.
	 */
	public static function currencyCode(): string
	{
		if (auth()->user()->vendor) {
			return auth()->user()->vendor->currency;
		}

		return Env::get('CHECKOUT_CURRENCY_CODE', 'USD');
	}

	/**
	 * Show amount with currency.
	 */
	public static function print(float $amount): string
	{
		return self::currencyCode() . " " . number_format($amount, 2);
	}
}
