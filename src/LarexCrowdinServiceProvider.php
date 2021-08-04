<?php

namespace Lukasss93\LarexCrowdin;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Lukasss93\LarexCrowdin\Support\Crowdin\Crowdin;

class LarexCrowdinServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/larex-crowdin.php' => config_path('larex-crowdin.php'),
        ], 'larex-crowdin-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/larex-crowdin.php', 'larex-crowdin');

        $this->app->singleton(Crowdin::class, function(Application $app){
            return new Crowdin([
                'access_token' => config('larex-crowdin.token'),
            ]);
        });

        $this->app->alias(Crowdin::class, 'crowdin');
    }
}
