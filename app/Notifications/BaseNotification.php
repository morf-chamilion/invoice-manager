<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

abstract class BaseNotification extends Notification
{
	/**
	 * Format mail template content with data.
	 */
	public static function formatMailContent(?string $template, array $content): string
	{
		foreach ($content as $key => $parameter) {
			$template = Str::replace($key, $parameter, $template);
		}

		return $template;
	}
}
