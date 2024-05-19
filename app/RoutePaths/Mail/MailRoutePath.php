<?php

namespace App\RoutePaths\Mail;

abstract class MailRoutePath
{
	public const COMMON = 'mail.common';

	public const VERIFICATION = 'mail.verification';

	public const PASSWORD_RESET = 'mail.password-reset';

	public const RECEIPT = 'mail.payment-receipt';

	public const RECEIVED = 'mail.payment-received';
}
