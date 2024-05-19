<?php

namespace App\Logs;

use Illuminate\Support\Facades\Log;
use Stringable;

class CheckoutLogger
{
	/**
	 * Channel for the logger.
	 */
	private static function channel(): string
	{
		return 'checkout';
	}

	/**
	 * Log checkout related information.
	 */
	public static function info(string | Stringable $message, array $context = []): void
	{
		Log::channel(self::channel())->info($message, $context);
	}

	/**
	 * Log checkout related exceptional occurrences that are not errors.
	 */
	public static function warning(string | Stringable $message, array $context = []): void
	{
		Log::channel(self::channel())->warning($message, $context);
	}

	/**
	 * Log checkout related runtime errors that do not require immediate action
	 * but should typically be logged and monitored.
	 */
	public static function error(string | Stringable $message, array $context = []): void
	{
		Log::channel(self::channel())->error($message, $context);
	}
}
