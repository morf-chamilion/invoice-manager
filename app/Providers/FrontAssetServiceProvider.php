<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FrontAssetServiceProvider extends ServiceProvider
{
    private int $directoryMode = 0775;

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
    public function boot(): void {}
}
