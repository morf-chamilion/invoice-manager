<?php

namespace App\Providers;

use App\Enums\SettingModule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use MatthiasMullie\Minify;
use lessc;

class FrontAssetServiceProvider extends ServiceProvider
{
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
    public function boot(): void
    {
        if (Schema::hasTable('settings') && settings(SettingModule::GENERAL)->get('compile_front_assets')) {

            $this->minifyCSS();

            $this->minifyJS();

            (new lessc())->compileFile(
                resource_path("css/front/less/master.less"),
                public_path("assets/css/front/master.css")
            );
        }
    }

    private function minifyCSS(): void
    {
        $minifier = new Minify\CSS();

        $minifier->add(public_path('assets/css/front/master.css'));

        $masterFilePath = public_path('assets/css/front/master.min.css');

        if (!File::exists($masterFilePath)) {
            File::makeDirectory(public_path('assets/css/front'), 0755, true);
            File::put($masterFilePath, '');
        }

        $minifier->minify($masterFilePath);
    }

    private function minifyJS(): void
    {
        $minifier = new Minify\JS();

        $minifier->add(resource_path('js/front/custom.js'));
        $minifier->add(resource_path('js/front/customer.js'));

        $masterFilePath = public_path('assets/js/front/custom-combined.min.js');

        if (!File::exists($masterFilePath)) {
            File::makeDirectory(public_path('assets/js/front'), 0775, true);
            File::put($masterFilePath, '');
        }

        $minifier->minify($masterFilePath);
    }
}
