<?php

namespace Leuverink\Bundle;

use Leuverink\Bundle\Bundlers\Bun;
use Illuminate\Support\Facades\Blade;
use Leuverink\Bundle\Components\Bundle;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Leuverink\Bundle\Contracts\BundleManager as BundleManagerContract;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bundle.php', 'bundle');

        $this->registerComponents();
    }

    public function register()
    {
        $this->registerBundleManager();


        // Only when using locally
        if (! $this->app->environment(['local', 'testing'])) {

            $this->publishes([
                __DIR__ . '/../config/bundle.php' => base_path('config/bundle.php'),
            ], 'bundle');
        }
    }

    protected function registerBundleManager()
    {
        $this->app->singleton(
            BundleManagerContract::class,
            fn () => new BundleManager(new Bun)
        );
    }

    protected function registerComponents()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/components', 'bundle');
        Blade::component('bundle', Bundle::class);
    }
}
