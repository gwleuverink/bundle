<?php

namespace Leuverink\Bundle\Tests;

use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Dusk\TestCase  as BaseTestCase;

class DuskTestCase extends BaseTestCase
{
    use WithWorkbench;

    /**
     * Renders a string into blade & navigates the Dusk browser to a temporary route
     * Then it returns the Browser object to continue chaining assertions.
     */
    public function blade(string $blade) {

        // Wrap in basic HTML layout
        $page = <<< BLADE
        <x-layout>
            $blade
        </x-layout>
        BLADE;

        Route::get('test-blade', fn() => $page);

        $return = null;
        $this->browse(function (Browser $browser) use (&$return) {
            $return = $browser->visit('test-blade');
        });

        return $return;
    }
}
