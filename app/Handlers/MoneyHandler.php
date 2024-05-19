<?php

namespace App\Handlers;

use Illuminate\Support\Env;
use Illuminate\Support\Number;

abstract class MoneyHandler
{
	/**
	 * Get the currency code.
	 */
	public static function currencyCode(): string
	{
		return Env::get('CHECKOUT_CURRENCY_CODE', 'USD');
	}

	/**
	 * Show amount with currency.
	 */
	public static function print(float $amount): string
	{
		return Number::currency($amount, self::currencyCode());
	}
}
