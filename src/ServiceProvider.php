<?php

namespace Leuverink\Bundle;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->environment(['local', 'testing'])) {
            return; // @codeCoverageIgnore
        }

        $this->publishes([
            __DIR__ . '/../config/bundle.php' => base_path('config/bundle.php'),
        ], 'bundle');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bundle.php', 'bundle');
    }
}
