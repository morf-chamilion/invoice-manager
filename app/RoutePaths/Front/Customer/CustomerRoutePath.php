<?php

namespace App\RoutePaths\Front\Customer;

abstract class CustomerRoutePath
{
	public const EDIT = 'front.customer.profile.edit';

	public const UPDATE = 'front.customer.profile.update';

	public const DASHBOARD_SHOW = 'front.customer.dashboard';

	public const INVOICE_INDEX = 'front.customer.invoice.index';

	public const INVOICE_SHOW = 'front.customer.invoice.show';

	public const INVOICE_DOWNLOAD = 'front.customer.invoice.download';
}
