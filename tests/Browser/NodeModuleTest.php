<?php

namespace Leuverink\Bundle\Tests\Browser;

use PHPUnit\Framework\Attributes\Test;
use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class NodeModuleTest extends DuskTestCase
{
    #[Test]
    public function it_injects_import_and_import_function_on_the_window_object()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="lodash/filter" as="filter" />
            HTML)
            ->assertScript('typeof window._import', 'function')
            ->assertScript('typeof window.x_import_modules', 'object');
    }

    #[Test]
    public function it_imports_from_node_modules()
    {
        $this->bladeString(<<< 'HTML'
            <x-import module="lodash" as="lodash" />

            <script type="module">
                const filter = await _import('lodash', 'filter');

                let data = [
                    { 'name': 'Foo', 'active': false },
                    { 'name': 'Hello World!', 'active': true }
                ];

                // Filter only active
                let filtered = filter(data, o => o.active)

                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('output').innerHTML = filtered[0].name
                })
            </script>

            <div id="output"></div>
        HTML)
            ->waitForTextIn('#output', 'Hello World!');
    }

    #[Test]
    public function it_can_import_modules_per_method()
    {
        $this->bladeString(<<< 'HTML'
            <x-import module="lodash/filter" as="filter" />

            <script type="module">
                const filter = await _import('filter');

                let data = [
                    { 'name': 'Foo', 'active': false },
                    { 'name': 'Yello World!', 'active': true }
                ];

                // Filter only active
                let filtered = filter(data, o => o.active)

                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('output').innerHTML = filtered[0].name
                })
            </script>

            <div id="output"></div>
        HTML)
            ->waitForTextIn('#output', 'Yello World!');
    }

    #[Test]
    public function it_can_use_both_local_and_node_module_together_on_the_same_page()
    {
        $this->bladeString(<<< 'HTML'
            <x-import module="~/output-to-id" as="output" />
            <x-import module="lodash/filter" as="filter" />

            <script type="module">
                const filter = await _import('filter');
                const output = await _import('output');

                let data = [
                    { 'name': 'Foo', 'active': false },
                    { 'name': 'Wello World!', 'active': true }
                ];

                // Filter only active
                let filtered = filter(data, o => o.active)

                output('output', filtered[0].name)

            </script>

            <div id="output"></div>
        HTML)
            ->waitForTextIn('#output', 'Wello World!');
    }
}
