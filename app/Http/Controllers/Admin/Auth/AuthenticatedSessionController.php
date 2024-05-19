<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Providers\AdminServiceProvider;
use App\RoutePaths\Admin\Auth\AuthRoutePath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends AdminBaseController
{
    public function __construct()
    {
        //
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        $this->sharePageData([
            'title' => 'Admin Login',
        ]);

        return view(AuthRoutePath::LOGIN);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(
            route(AdminServiceProvider::DASHBOARD_ROUTE)
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard(AdminServiceProvider::GUARD)->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route(AuthRoutePath::LOGIN);
    }
}
