<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Harden Eloquent's behavior to be strict.
        Model::preventSilentlyDiscardingAttributes(!App::isProduction());
        Model::preventAccessingMissingAttributes(!App::isProduction());
        // Model::preventLazyLoading(!App::isProduction());

        self::bootSettings();
    }

    /**
     * Boot method for hydrating settings.
     */
    private static function bootSettings(): mixed
    {
        try {
            if (App::isProduction()) {
                $settings = Cache::remember(
                    key: 'settings',
                    ttl: config('settings.cache_ttl'),
                    callback: fn () => Setting::all()->keyBy('module'),
                );
            } else {
                $settings = Setting::all()->keyBy('module');
            }
        } catch (QueryException $e) {
            report($e);

            $settings = new Collection([]);
        }

        return View::share('settings', $settings);
    }
}
