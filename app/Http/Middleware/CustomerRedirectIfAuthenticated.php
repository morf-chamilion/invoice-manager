<?php

namespace App\Http\Middleware;

use App\Providers\CustomerServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard(CustomerServiceProvider::GUARD)->check()) {
            return redirect()->route(
                CustomerServiceProvider::LOGIN_ROUTE,
                ['key' => urlencode($request->url())]
            );
        }

        return $next($request);
    }
}
