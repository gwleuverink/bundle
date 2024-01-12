<?php

namespace Leuverink\Bundle\Tests\Browser;

use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class LocalModuleTest extends DuskTestCase
{
    /** @test */
    public function it_injects_import_and_bundle_function_on_the_window_object()
    {
        $this->blade(<<< 'HTML'
                <x-bundle import="~/alert" as="alert" />
            HTML)
            ->assertScript('typeof window._bundle', 'function')
            ->assertScript('typeof window._bundle_modules', 'object');
    }

    /** @test */
    public function it_imports_from_local_resource_directory()
    {
        $this->blade(<<< 'HTML'
                <x-bundle import="~/alert" as="alert" />

                <script type="module">
                    var module = await _bundle('alert');
                    module('Hello World!')
                </script>
            HTML)
            ->assertDialogOpened('Hello World!');
    }
}
