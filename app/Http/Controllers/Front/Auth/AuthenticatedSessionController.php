<?php

namespace App\Http\Controllers\Front\Auth;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\Http\Requests\Front\Auth\LoginRequest;
use App\Providers\CustomerServiceProvider;
use App\RoutePaths\Front\Auth\AuthRoutePath;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends FrontBaseController
{
    public function __construct(
        private SettingService $settingService,
    ) {
        parent::__construct(settingService: $settingService);
    }

    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $this->sharePageData(SettingModule::HOME);

        $request->session()->put('key', $request->query('key'));

        return view(AuthRoutePath::LOGIN);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $intendedURL = $request->session()->get('key');

        $request->session()->regenerate();

        if ($intendedURL) {
            return redirect()->intended(urldecode($intendedURL));
        }

        return redirect()->route(CustomerRoutePath::DASHBOARD_SHOW);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard(CustomerServiceProvider::GUARD)->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route(AuthRoutePath::LOGIN);
    }
}
