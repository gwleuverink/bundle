<?php

namespace Leuverink\Bundle\Tests\Browser;

use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class LocalModuleTest extends DuskTestCase
{
    /** @test */
    public function it_injects_import_and_import_function_on_the_window_object()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/alert" as="alert" />
            HTML)
            ->assertScript('typeof window._import', 'function')
            ->assertScript('typeof window.x_import_modules', 'object');
    }

    /** @test */
    public function it_imports_from_local_resource_directory()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/alert" as="alert" />

                <script type="module">
                    var module = await _import('alert');
                    module('Hello World!')
                </script>
            HTML)
            ->assertDialogOpened('Hello World!');
    }

    /** @test */
    public function it_canx_import_modules_per_method()
    {

    }
}
