<?php

namespace Leuverink\Bundle\Tests;

use Override;
use Livewire\Livewire;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Dusk\Options;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;
use Orchestra\Testbench\Http\Middleware\VerifyCsrfToken;

class DuskTestCase extends BaseTestCase
{
    const CLEAR_AFTER_TEST = true;
    const WITHOUT_UI = true;

    use WithWorkbench;

    // protected function defineEnvironment($app)
    // {
    //     // Workaround Testbench Dusk issue dropping registered config (since v9)
    //     tap($app['config'], function (Repository $config) {
    //         $config->set('bundle', require __DIR__ . '/../config/bundle.php');
    //     });
    // }

    #[Override]
    public static function setUpBeforeClass(): void
    {
        if (static::WITHOUT_UI) {
            Options::withoutUI();
        }

        parent::setUpBeforeClass();
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        if (static::CLEAR_AFTER_TEST) {
            $this->artisan('view:clear');
            $this->artisan('bundle:clear');
        }

        // Workaround Testbench Dusk issue dropping registered config (since v9)
        // config([
        //     'bundle' => require __DIR__ . '/../config/bundle.php',
        // ]);
    }

    #[Override]
    protected function tearDown(): void
    {
        if (static::CLEAR_AFTER_TEST) {
            $this->artisan('view:clear');
            $this->artisan('bundle:clear');
        }

        parent::tearDown();
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        // SOMEHOW FIXES THE ISSUE INSTEAD OF WORKAROUND BELOW
        // Can  be anything that reaches the container, like dump
        // Annoying to debug, since the problem doesn't exist when dumping
        config('bundle');

        // TEMPORARY WORKAROUND
        // Original teardownAfterClass hangs because chromedriver cannot be killed?
        // Fringe error. It exits the current process suddenly without exceptions.
        // The chrome driver & server stay alive, so additional test classes can't run
        // $serverPort = static::$baseServePort;
        // $pid = Process::run("kill (lsof -t -i :{$serverPort})")->throw()->output();
        // Process::run("kill {$pid}")->throw();

        // $chromeDriverPort = static::$chromeDriverPort;
        // $pid = Process::run("kill (lsof -t -i :{$chromeDriverPort})")->throw()->output();
        // Process::run("kill {$pid}")->throw();
        // END TEMPORARY WORKAROUND

        parent::tearDownAfterClass();
    }

    protected function getBasePath()
    {
        // testbench-core skeleton is leading, due to test setup in testbench.yaml
        return __DIR__ . '/../vendor/orchestra/testbench-core/laravel';
    }

    /**
     * Renders a string into blade & navigates the Dusk browser to a temporary route
     * Then it returns the Browser object to continue chaining assertions.
     */
    public function bladeString(string $blade)
    {
        // Wrap in basic HTML layout (include required js & css in layout if needed)
        $blade = <<< BLADE
        <x-layout>
            {$blade}
        </x-layout>
        BLADE;

        // Render the blade
        $page = Blade::render($blade);

        // Create a temporary route
        $this->beforeServingApplication(function ($app, $config) use ($page) {
            $config->set('app.debug', true);
            $config->set('bundle.cache_control_headers', 'no-cache, no-store, must-revalidate');

            $app->make(Route::class)::get('test-blade', fn () => $page);
        });

        // Point Dusk to the temporary route & return the Browser for chaining
        $return = null;
        $this->browse(function (Browser $browser) use (&$return) {
            $return = $browser->visit('test-blade');
        });

        return $return;
    }

    /**
     * Navigates the Dusk browser to a temporary route to a Livewire page component
     * Then it returns the Browser object to continue chaining assertions.
     */
    public function serveLivewire($component)
    {
        $this->artisan('livewire:layout');

        // Create a temporary route
        $this->beforeServingApplication(function ($app, $config) use (&$component) {
            $config->set('app.debug', true);
            $config->set('app.key', 'base64:q1fQla64BmAKJBOnRKuXvfddVoqEuSLv1eOEEO91uGI=');
            $config->set('bundle.cache_control_headers', 'no-cache, no-store, must-revalidate');

            // Needs to register so component is findable in update calls
            Livewire::component($component);

            // Disable CSRF check from update route
            Livewire::setUpdateRoute(function ($handle) use ($app) {
                return $app->make(Route::class)::post('/livewire/update', $handle)->withoutMiddleware(VerifyCsrfToken::class);
            });

            // Register temporary Livewire route
            $app->make(Route::class)::get('test-livewire', $component);
        });

        // Point Dusk to the temporary route & return the Browser for chaining
        $return = null;
        $this->browse(function (Browser $browser) use (&$return) {
            $return = $browser->visit('test-livewire');
        });

        return $return;
    }
}
