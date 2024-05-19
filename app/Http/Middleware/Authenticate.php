<?php

namespace App\Http\Middleware;

use App\RoutePaths\Admin\Auth\AuthRoutePath;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route(AuthRoutePath::LOGIN);
    }
}
