<?php

namespace App\Providers;

use App\Repositories\PageRepository;
use App\Services\PageService;
use App\Services\SettingService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class PageServiceProvider extends ServiceProvider
{
    public const TEMPLATE_DEFAULT_ADMIN_VIEW = 'admin.page.template.default';
    public const TEMPLATE_DEFAULT_FRONT_VIEW = 'front.page.default';

    /**
     * Define page templates.
     */
    public function registerPageTemplates()
    {
        return [
            'Default' => $this->viewPaths(
                front: 'page.default',
                admin: 'page.template.default',
            ),
        ];
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PageService::class, function (Application $app) {
            $pageService = new PageService(
                $app->make(PageRepository::class),
                $app->make(SettingService::class),
            );

            $pageService->setPageTemplates($this->registerPageTemplates());

            return $pageService;
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
     * Create page template admin and front view paths.
     */
    protected function viewPaths(string $front, string $admin): array
    {
        return [
            'front' => 'front.' . $front,
            'admin' => 'admin.' . $admin,
        ];
    }
}
