<?php

namespace Leuverink\Bundle\Tests;

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
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('view:clear');
        $this->artisan('bundle:clear');
    }

    public static function setUpBeforeClass(): void
    {
        Options::withoutUI();
        parent::setUpBeforeClass();
    }

    protected function tearDown(): void
    {
        $this->artisan('bundle:clear');
        $this->artisan('view:clear');

        parent::tearDown();
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
    public function blade(string $blade)
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

            // Needs to register so component is findable in update calls
            Livewire::component($component);

            // Disable CSRF check from update route
            Livewire::setUpdateRoute(function ($handle) {
                return Route::post('/livewire/update', $handle)->withoutMiddleware(VerifyCsrfToken::class);
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
