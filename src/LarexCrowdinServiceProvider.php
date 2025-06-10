<?php

namespace Lukasss93\LarexCrowdin;

use Illuminate\Support\ServiceProvider;
use Lukasss93\LarexCrowdin\Commands\LanguagesListCommand;
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
        $this->commands([
            LanguagesListCommand::class,
        ]);

        $this->mergeConfigFrom(__DIR__.'/config/larex-crowdin.php', 'larex-crowdin');

        $this->app->singleton(Crowdin::class, fn () => new Crowdin(array_filter([
            'access_token' => config('larex-crowdin.token'),
            'organization' => config('larex-crowdin.organization'),
        ])));

        $this->app->alias(Crowdin::class, 'crowdin');
    }
}
