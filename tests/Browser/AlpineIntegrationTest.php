<?php

namespace Leuverink\Bundle\Tests\Browser;

use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class AlpineIntegrationTest extends DuskTestCase
{
    /** @test */
    public function it_can_bootstrap_alpine_via_import()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine" />

            <div
                id="component"
                x-text="message"
                x-data="{
                    message: 'Hello World!'
                }"
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->assertSeeIn('#component', 'Hello World!');
    }

    /** @test */
    public function it_can_bootstrap_plugins_via_import()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine" />

            <div
                id="component"
                x-text="message"
                x-data="{
                    message: 'Hello World!'
                }"
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->assertSeeIn('#component', 'Hello World!');
    }

    /** @test */
    public function it_can_use_other_imports_inside_x_init_directive()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine" />
            <x-import module="lodash/filter" as="filter" />

            <div
                id="component"
                x-init="
                    const filter = await _import('filter');

                    let data = [
                        { 'name': 'Foo', 'active': false },
                        { 'name': 'Fello World!', 'active': true }
                    ];

                    // Filter only active
                    let filtered = filter(data, o => o.active)

                    $el.innerHTML = filtered[0].name
                "
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->assertSee('Fello World!');

    }

    /** @test */
    public function it_can_use_other_imports_inside_x_data_directive()
    {
        $browser = $this->blade(<<< 'HTML'

            <x-import module="~/bootstrap/alpine" />
            <x-import module="lodash/filter" as="filter" />

            <div
                x-data="{
                     async init() {
                        const filter = await _import('filter');

                        let data = [
                            { 'name': 'Foo', 'active': false },
                            { 'name': 'Gello World!', 'active': true }
                        ];

                        // Filter only active
                        let filtered = filter(data, o => o.active)

                        $el.innerHTML = filtered[0].name
                    }
                }"
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->assertSee('Gello World!');
    }
}
