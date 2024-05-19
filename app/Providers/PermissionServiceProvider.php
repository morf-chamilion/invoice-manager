<?php

namespace App\Providers;

use App\Repositories\PermissionRepository;
use App\Services\PermissionService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /** 
     * Define resources to enforce permissions.
     */
    protected array $routePaths = [
        \App\RoutePaths\Admin\User\UserRoutePath::class,
        \App\RoutePaths\Admin\User\UserRoleRoutePath::class,

        \App\RoutePaths\Admin\Setting\SettingRoutePath::class,
        // \App\RoutePaths\Admin\Setting\PageSettingRoutePath::class,
        // \App\RoutePaths\Admin\Page\PageRoutePath::class,

        \App\RoutePaths\Admin\Invoice\InvoiceRoutePath::class,
        \App\RoutePaths\Admin\Customer\CustomerRoutePath::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PermissionService::class, function (Application $app) {
            $permissionService = new PermissionService(
                $app->make(PermissionRepository::class)
            );

            $permissionService->setRoutePaths($this->mapRoutePaths());

            return $permissionService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Create instances from class names.
     */
    protected function mapRoutePaths(): array
    {
        return collect($this->routePaths)->mapWithKeys(function (string $className) {
            return [$className => $this->app->make($className)];
        })->all();
    }
}
