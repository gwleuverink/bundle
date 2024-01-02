<?php

namespace Leuverink\Bundle;

use Leuverink\Bundle\Bundlers\Bun;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Leuverink\Bundle\Contracts\BundleManager as BundleManagerContract;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(
            BundleManagerContract::class,
            fn () => new BundleManager(new Bun)
        );



        // Only when using locally
        if (! $this->app->environment(['local', 'testing'])) {

            $this->publishes([
                __DIR__ . '/../config/bundle.php' => base_path('config/bundle.php'),
            ], 'bundle');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bundle.php', 'bundle');
    }
}
