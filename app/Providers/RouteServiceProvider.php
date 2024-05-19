<?php

namespace App\Providers;

use App\Services\PageService;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Controllers\Front\Page\PageController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     */
    public const HOME = '/admin/dashboard';

    /**
     * Define the front routes for the application.
     */
    protected const FRONT_ROUTES = [
        'auth',
        'checkout',
        'customer',
    ];

    /**
     * Define the admin routes for the application.
     */
    protected const ADMIN_ROUTES = [
        'admin',
        'users',
        'user-roles',
        'auth',
        'settings',
        'pages',
        'invoices',
        'customers',
    ];

    /**
     * Define the common routes for the application.
     */
    protected const COMMON_ROUTES = [
        'media',
    ];

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->mapFrontRoutes();
        $this->mapAdminRoutes();
        $this->mapCommonRoutes();
        $this->registerFrontPageRoutes();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            if (App::isLocal()) {
                Route::middleware('web')
                    ->group(base_path('routes/sandbox.php'));
            }
        });
    }

    /**
     * Map front routes of the application.
     */
    protected function mapFrontRoutes(): void
    {
        if (!empty($this::FRONT_ROUTES)) {
            foreach ($this::FRONT_ROUTES as $route) {
                Route::middleware('web')
                    ->group(base_path('routes/front/' . $route . '.php'));
            }
        }
    }

    /**
     * Map admin routes of the application.
     */
    protected function mapAdminRoutes(): void
    {
        if (!empty($this::ADMIN_ROUTES)) {
            foreach ($this::ADMIN_ROUTES as $route) {
                Route::prefix('admin')
                    ->middleware('admin')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/admin/' . $route . '.php'));
            }
        }
    }

    /**
     * Map common routes of the application.
     */
    protected function mapCommonRoutes(): void
    {
        if (!empty($this::COMMON_ROUTES)) {
            foreach ($this::COMMON_ROUTES as $route) {
                Route::middleware('web')
                    ->group(base_path('routes/common/' . $route . '.php'));
            }
        }
    }

    /**
     * Register dynamic front page routes in runtime.
     */
    protected function registerFrontPageRoutes(): void
    {
        /** @var PageService $pageService  */
        $pageService = App::make(PageService::class);

        if (!Schema::hasTable('pages')) {
            return;
        }

        try {
            $pageService->getAllpages()->each(function ($page) {
                Route::get('/' . $page->slug, PageController::class)
                    ->middleware('web')
                    ->name(PageService::pageRouteName($page));
            });
        } catch (BindingResolutionException  $e) {
            throw new BindingResolutionException($e->getMessage());
        }
    }
}
