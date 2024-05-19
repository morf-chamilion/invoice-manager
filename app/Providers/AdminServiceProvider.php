<?php

namespace App\Providers;

use App\Models\User;
use App\RoutePaths\Admin\AdminRoutePath;
use App\RoutePaths\Admin\Auth\AuthRoutePath;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * admin guard name.
     *
     * @var string
     */
    public const GUARD = 'admin';

    /**
     * Login route name.
     */
    public const LOGIN_ROUTE = AuthRoutePath::LOGIN;

    /**
     * Dashboard route name.
     */
    public const DASHBOARD_ROUTE = AdminRoutePath::DASHBOARD;

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the currently authenticated user.
     */
    public static function getAuthUser(): ?User
    {
        if (!Auth::guard(self::GUARD)->check()) {
            return null;
        }

        return Auth::guard(self::GUARD)->user();
    }
}
