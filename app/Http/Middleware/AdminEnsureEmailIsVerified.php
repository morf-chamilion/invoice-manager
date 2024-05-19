<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Providers\AdminServiceProvider;
use App\RoutePaths\Admin\Auth\AuthRoutePath;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class AdminEnsureEmailIsVerified
{
    /**
     * Get the authenticated user.
     */
    public function authUser(): ?User
    {
        return AdminServiceProvider::getAuthUser();
    }

    /**
     * Specify the redirect route for the middleware.
     *
     * @param  string  $route
     * @return string
     */
    public static function redirectTo($route)
    {
        return static::class . ':' . $route;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (
            !$this->authUser() ||
            ($this->authUser() instanceof MustVerifyEmail &&
                !$this->authUser()->hasVerifiedEmail())
        ) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: AuthRoutePath::VERIFICATION_NOTICE));
        }

        return $next($request);
    }
}
