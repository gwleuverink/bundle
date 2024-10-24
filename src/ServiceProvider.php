<?php

// @codeCoverageIgnoreStart

namespace Leuverink\Bundle;

use Leuverink\Bundle\Commands\Build;
use Leuverink\Bundle\Commands\Clear;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Leuverink\Bundle\Bundlers\Bun\Bun;
use Leuverink\Bundle\Commands\Install;
use Leuverink\Bundle\Commands\Version;
use Leuverink\Bundle\Components\Import;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Leuverink\Bundle\Contracts\BundleManager as BundleManagerContract;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        // Only when using locally
        if (! $this->app->environment(['local', 'testing'])) {

            $this->publishes([
                __DIR__ . '/../config/bundle.php' => config_path('bundle.php'),
            ], 'bundle');
        }

        $this->registerComponents();
        $this->registerCommands();
        $this->injectCore();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bundle.php', 'bundle');

        $this->registerBundleManager();
        $this->registerRoutes();
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
        $this->loadViewsFrom(__DIR__ . '/Components/views', 'x-import');
        Blade::component('import', Import::class);
    }

    protected function injectCore()
    {
        Event::listen(
            RequestHandled::class,
            InjectCore::class,
        );
    }

    protected function registerCommands()
    {
        $this->commands(Install::class);
        $this->commands(Version::class);
        $this->commands(Build::class);
        $this->commands(Clear::class);
    }

    protected function registerRoutes()
    {
        Route::get(
            'x-import/{bundle}',
            fn ($bundle) => resolve(BundleManagerContract::class)->bundleContents($bundle)
        )->name('bundle:import');

        // TODO: Support code splitting
        // Route::get(
        //     'x-import/chunks/{chunk}',
        //     fn($chunk) => resolve(BundleManagerContract::class)->chunkContents($chunk)
        // );
    }
}

// @codeCoverageIgnoreEnd
