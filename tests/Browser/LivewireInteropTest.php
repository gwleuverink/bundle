<?php

namespace Leuverink\Bundle\Tests\Browser;

use Livewire\Component;
use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class LivewireInteropTest extends DuskTestCase
{
    /** @test */
    public function it_injects_import_and_import_function_on_the_window_object()
    {
        $browser = $this->serveLivewire(ImportsLodashFilter::class);

        $browser
            ->assertScript('typeof window._import', 'function')
            ->assertScript('typeof window.x_import_modules', 'object');
    }

    /** @test */
    public function it_imports_from_node_modules()
    {
        $browser = $this->serveLivewire(ImportsLodashFilter::class);

        $browser->assertScript('typeof window.x_import_modules.filter', 'object');
    }

    /** @test */
    public function it_imports_modules_inside_assets_directive()
    {
        $browser = $this->serveLivewire(ImportsLodashFilterInsideAssetsDirective::class);

        $browser
            ->assertScript('typeof window._import', 'function')
            ->assertScript('typeof window.x_import_modules', 'object')
            ->assertScript('typeof window.x_import_modules.filter', 'object');
    }

    /** @test */
    public function it_can_use_imports_from_x_init()
    {
        $browser = $this->serveLivewire(CanUseImportsFromXInit::class);

        $browser->waitForTextIn('#component', 'Hello from x-init!');
    }

    /** @test */
    public function it_can_use_imports_from_x_data()
    {
        $browser = $this->serveLivewire(CanUseImportsFromXData::class);

        $browser->waitForTextIn('#component', 'Hello from x-data!');
    }

    /** @test */
    public function it_can_use_imports_from_x_click_listener()
    {
        $browser = $this->serveLivewire(CanUseImportsFromClickListener::class);

        $browser
            ->waitForTextIn('#component', 'Click to change text')
            ->press('#component')
            ->waitForTextIn('#component', 'Hello from x-on:click!');
    }

    /** @test */
    public function it_can_use_imports_in_actions_using_one_time_js_expressions()
    {
        $browser = $this->serveLivewire(CanUseImportsFromAction::class);

        $browser
            ->waitForTextIn('#component', 'Text changes when the Livewire action is invoked')
            // ->waitForLivewire()
            ->click('@action')
            ->waitForTextIn('#component', 'Hello from wire action!');
    }
}

//--------------------------------------------------------------------------
// Components
//--------------------------------------------------------------------------
class ImportsLodashFilter extends Component
{
    public function render()
    {
        return <<< 'HTML'
            <div>
                <x-import module="lodash/filter" as="filter" />
            </div>
        HTML;
    }
}

class ImportsLodashFilterInsideAssetsDirective extends Component
{
    public function render()
    {
        return <<< 'HTML'
            <div>
                @assets
                <x-import module="lodash/filter" as="filter" />
                @assets
            </div>
        HTML;
    }
}

class CanUseImportsFromXInit extends Component
{
    public function render()
    {
        return <<< 'HTML'
            <div>
                <x-import module="~/invokes-callable" as="invoke" />

                <div id="component"
                    x-init="
                        const invoke = await _import('invoke')

                        invoke(() => $el.innerHTML = 'Hello from x-init!')
                    "
                ></div>
            </div>
        HTML;
    }
}

class CanUseImportsFromXData extends Component
{
    public function render()
    {
        return <<< 'HTML'
            <div>
                <x-import module="~/invokes-callable" as="invoke" />

                <div id="component"
                    x-data="{
                        async init() {
                            const invoke = await _import('invoke')

                            invoke(() => $el.innerHTML = 'Hello from x-data!')
                        }
                    }"
                ></div>
            </div>
        HTML;
    }
}

class CanUseImportsFromClickListener extends Component
{
    public function render()
    {
        return <<< 'HTML'
            <div>
                <x-import module="~/invokes-callable" as="invoke" />

                <button id="component"
                    x-on:click="
                        const invoke = await _import('invoke')

                        invoke(() => $el.innerHTML = 'Hello from x-on:click!')
                    "
                >Click to change text</button>
            </div>
        HTML;
    }
}

class CanUseImportsFromAction extends Component
{
    public function action()
    {
        $this->js(<<< 'JS'
            const invoke = await _import('invoke')

            invoke(() => document.getElementById('component').innerHTML = 'Hello from wire action!')
        JS);
    }

    public function render()
    {
        return <<< 'HTML'
            <div>
                <x-import module="~/invokes-callable" as="invoke" />

                <button wire:click="action" dusk="action">Call action</button>

                <div id="component">
                    Text changes when the Livewire action is invoked
                </div>
            </div>
        HTML;
    }
}
