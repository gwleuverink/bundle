<?php

namespace Leuverink\Bundle\Tests\Browser;

use PHPUnit\Framework\Attributes\Test;
use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class LocalModuleTest extends DuskTestCase
{
    #[Test]
    public function it_injects_import_and_import_function_on_the_window_object()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" />
            HTML)
            ->assertScript('typeof window._import', 'function')
            ->assertScript('typeof window.x_import_modules', 'object');
    }

    #[Test]
    public function it_evaluates_function_expressions()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/function-is-evaluated" as="is-evaluated" defer />
            HTML)
            ->assertScript('window.test_evaluated', true);
    }

    #[Test]
    public function it_can_import_modules_inside_function_expressions()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/function-is-evaluated" as="is-evaluated" defer />
            HTML)
            ->assertScript('window.test_evaluated', true);
    }

    #[Test]
    public function it_imports_from_local_resource_directory()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" />

                <script type="module">
                    var output = await _import('output');
                    output('output', 'Yello World!')
                </script>

                <div id="output"></div>
            HTML)
            ->waitForTextIn('#output', 'Yello World!');
    }

    #[Test]
    public function it_can_import_named_functions()
    {
        $this->bladeString(<<< 'HTML'
            <x-import module="~/named-functions" as="helpers" />

            <script type="module">
                const outputBar = await _import('helpers', 'bar');

                outputBar()
            </script>

            <div id="output"></div>
        HTML)
            ->waitForTextIn('#output', 'Bar');
    }
}
