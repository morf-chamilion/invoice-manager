<?php

namespace App\Providers;

use App\Models\Customer;
use App\RoutePaths\Front\Auth\AuthRoutePath;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * admin guard name.
     *
     * @var string
     */
    public const GUARD = 'customer';

    /**
     * Login route name.
     */
    public const LOGIN_ROUTE = AuthRoutePath::LOGIN;

    /**
     * Dashboard route name.
     */
    public const DASHBOARD_ROUTE = CustomerRoutePath::DASHBOARD_SHOW;

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
    public static function getAuthUser(): ?Customer
    {
        if (!Auth::guard(self::GUARD)->check()) {
            return null;
        }

        return Auth::guard(self::GUARD)->user();
    }
}
