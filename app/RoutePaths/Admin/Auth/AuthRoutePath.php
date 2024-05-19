<?php

namespace App\RoutePaths\Admin\Auth;

class AuthRoutePath
{
	public const LOGIN = 'admin.auth.login';

	public const LOGOUT = 'admin.auth.logout';

	public const LOGIN_STORE = 'admin.auth.login.store';

	public const PASSWORD_RESET = 'admin.auth.password.reset';

	public const PASSWORD_FORGET = 'admin.auth.password.forget';

	public const PASSWORD_CONFIRM = 'admin.auth.password.confirm';

	public const PASSWORD_UPDATE = 'admin.auth.password.update';

	public const PASSWORD_MAIL = 'admin.auth.password.mail';

	public const PASSWORD_STORE = 'admin.auth.password.store';

	public const VERIFICATION_NOTICE = 'admin.auth.verification.notice';

	public const VERIFICATION_SHOW = 'admin.auth.verification.show';

	public const VERIFICATION_VERIFY = 'admin.auth.verification.verify';

	public const VERIFICATION_SEND = 'admin.auth.verification.send';

	public const VERIFICATION_CONFIRM = 'admin.auth.verification.confirm';
}
