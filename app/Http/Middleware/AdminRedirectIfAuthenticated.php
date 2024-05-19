<?php

namespace App\Http\Middleware;

use App\Providers\AdminServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AdminRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard(AdminServiceProvider::GUARD)->check()) {
            return redirect()->route(
                AdminServiceProvider::LOGIN_ROUTE,
                ['key' => Str::random(60)]
            );
        }

        return $next($request);
    }
}
