<?php

namespace App\Handlers;

abstract class MoneyHandler
{
	/**
	 * Get the currency code.
	 */
	public static function currencyCode(): string
	{
		return config('checkout.currency_code');
	}

	/**
	 * Show amount with currency.
	 */
	public static function print(float $amount): string
	{
		return self::currencyCode() . " " . self::format($amount);
	}

	/**
	 * Format amount.
	 */
	public static function format(float $amount): string
	{
		return number_format($amount, 2);
	}
}
