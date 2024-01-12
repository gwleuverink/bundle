<?php

namespace Leuverink\Bundle\Tests\Browser;

use Laravel\Dusk\Browser;
use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class NodeModuleTest extends DuskTestCase
{
    /** @test */
    public function it_injects_import_and_bundle_function_on_the_window_object() {
        $this->blade(<<< HTML
                <x-bundle import="lodash/filter" as="filter" />
            HTML)
            ->assertScript("typeof window._bundle", 'function')
            ->assertScript('typeof window._bundle_modules', 'object');
    }

    /** @test */
    public function it_imports_from_node_modules() {
        $this->blade(<<< HTML
            <x-bundle import="lodash" as="lodash" />

            <script type="module">
                const filter = await _bundle('lodash', 'filter');

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
        ->assertSeeIn('#output', 'Hello World!');
    }

    /** @test */
    public function it_can_import_modules_per_method() {
        $this->blade(<<< HTML
            <x-bundle import="lodash/filter" as="filter" />

            <script type="module">
                const filter = await _bundle('filter');

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
        ->assertSeeIn('#output', 'Yello World!');
    }
}
