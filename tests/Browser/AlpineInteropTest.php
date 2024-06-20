<?php

namespace Leuverink\Bundle\Tests\Browser;

use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class AlpineInteropTest extends DuskTestCase
{
    /** @test */
    public function it_can_bootstrap_alpine_via_iife_import()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine-iife" />

            <div
                id="component"
                x-text="message"
                x-data="{
                    message: 'Alpine loaded!'
                }"
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->waitForTextIn('#component', 'Alpine loaded!');
    }

    /** @test */
    public function it_can_bootstrap_plugins_via_iife_import()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine-iife-with-plugin" />

            <div
                id="component"
                x-text="message"
                x-data="{
                    message: typeof Alpine.persist === 'function'
                        ? 'Plugin loaded!'
                        : false
                }"
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->waitForTextIn('#component', 'Plugin loaded!');
    }

    /** @test */
    public function it_can_bootstrap_alpine_via_initable_import()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine-init" init />

            <div
                id="component"
                x-text="message"
                x-data="{
                    message: 'Alpine loaded!'
                }"
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->waitForTextIn('#component', 'Alpine loaded!');
    }

    /** @test */
    public function it_can_bootstrap_plugins_via_initable_import()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine-init-with-plugin" init />

            <div
                id="component"
                x-text="message"
                x-data="{
                    message: typeof Alpine.persist === 'function'
                        ? 'Plugin loaded!'
                        : false
                }"
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->waitForTextIn('#component', 'Plugin loaded!');
    }

    /** @test */
    public function it_can_use_imports_from_x_init()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine-init" init />
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

        $browser->waitForTextIn('#component', 'Fello World!');

    }

    /** @test */
    public function it_can_use_imports_from_x_data()
    {
        $browser = $this->blade(<<< 'HTML'

            <x-import module="~/bootstrap/alpine-init" init />
            <x-import module="lodash/filter" as="filter" />

            <div
                id="component"
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

        $browser->waitForTextIn('#component', 'Gello World!');
    }

    /** @test */
    public function it_can_use_imports_from_x_click_listener()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/bootstrap/alpine-init" init />
            <x-import module="lodash/filter" as="filter" />

            <button
                id="component"
                x-data
                x-on:click="
                    const filter = await _import('filter');

                    let data = [
                        { 'name': 'Foo', 'active': false },
                        { 'name': 'Cello World!', 'active': true }
                    ];

                    // Filter only active
                    let filtered = filter(data, o => o.active)

                    $el.innerHTML = filtered[0].name
                "
            >Click to change text</button>
        HTML);

        $browser
            ->waitForTextIn('#component', 'Click to change text')
            ->press('#component')
            ->waitForTextIn('#component', 'Cello World!');

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

    }

    /** @test */
    public function it_supports_backed_components_with_alpine_data()
    {
        $this->markTestSkipped('not implemented');

        $browser = $this->blade(<<< 'HTML'

            <x-import module="~/bootstrap/alpine-init" init />
            <x-import module="~/components/hello-world" />

            <div
                x-data="hello-world"
                x-text="message"
                id="component"
            ></div>
        HTML);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));

        $browser->waitForTextIn('#component', 'Hello backed component!');
    }
}
