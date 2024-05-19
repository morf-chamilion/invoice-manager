<?php

namespace App\Http\Controllers\Front\Auth;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\RoutePaths\Front\Auth\AuthRoutePath;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends FrontBaseController
{
    public function __construct(
        private SettingService $settingService,
    ) {
        parent::__construct(settingService: $settingService);
    }

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        $this->sharePageData(SettingModule::GENERAL);

        return view(AuthRoutePath::PASSWORD_FORGET);
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse|ValidationException
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::broker('customer')->sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
