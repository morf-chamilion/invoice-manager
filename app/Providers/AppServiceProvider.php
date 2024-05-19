<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
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
        if (Schema::hasTable((new Setting)->getTable())) {
            if (App::isProduction()) {
                $settings = Cache::remember(
                    key: 'settings',
                    ttl: Env::get('SETTINGS_CACHE_TTL', 600),
                    callback: fn () => Setting::all()->keyBy('module'),
                );
            } else {
                $settings = Setting::all()->keyBy('module');
            }

            return View::share('settings', $settings);
        }

        return View::share('settings', new Collection([]));
    }
}
