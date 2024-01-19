<?php

namespace Leuverink\Bundle\Tests\Browser;

use Illuminate\View\ViewException;
use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

// Old syntax as a reference:

// it('renders the same import only once')
//     ->blade(<<< HTML
//         <x-import module="~/alert" as="alert" />
//         <x-import module="~/alert" as="alert" />
//     HTML)
//     ->assertScript(<<< JS
//         document.querySelectorAll('script[data-bundle="alert"').length
//     JS, 1);

class ComponentTest extends DuskTestCase
{
    /** @test */
    public function it_renders_the_same_import_only_once()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" />
                <x-import module="~/output-to-id" as="output" />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-bundle="~/output-to-id"').length
            JS, 1);
    }

    /** @test */
    public function it_renders_the_same_import_only_once_when_one_was_inlined()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" />
                <x-import module="~/output-to-id" as="output" inline />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-bundle="~/output-to-id"').length
            JS, 1);
    }

    /** @test */
    public function it_renders_multiple_imports_when_they_only_use_a_module_property()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/function-is-evaluated" />
                <x-import module="~/output-to-id" />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-bundle="~/function-is-evaluated"').length
            JS, 1)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-bundle="~/output-to-id"').length
            JS, 1);
    }

    /** @test */
    public function it_renders_the_same_import_under_different_aliases()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/output-to-id" as="foo" />
                <x-import module="~/output-to-id" as="bar" />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-bundle="~/output-to-id"').length
            JS, 2);
    }

    /** @test */
    public function it_renders_script_inline_when_inline_prop_was_passed()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" inline />
            HTML)
            // Assert it doesn't render src attribute on the script tag
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-bundle="~/output-to-id"')[0].hasAttribute('src')
            JS, false)
            // Assert script tag has content
            ->assertScript(<<< 'JS'
                typeof document.querySelectorAll('script[data-bundle="~/output-to-id"')[0].innerHTML
            JS, 'string');
    }

    public function it_doesnt_render_script_inline_by_default()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" />
            HTML)
            // Assert it renders src attribute on the script tag
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-bundle="~/output-to-id"')[0].hasAttribute('src')
            JS, true)
            // Assert script tag has no content
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-bundle="~/output-to-id"')[0].innerHTML
            JS, null);
    }

    /** @test */
    public function it_works_when_a_iife_is_combined_with_multiple_aliased_imports()
    {
        $browser = $this->blade(<<< 'HTML'
                <x-import module="~/function-is-evaluated" />
                <x-import module="~/named-functions" as="helpers" />
                <x-import module="~/alert" as="alert" />

                <script type="module">
                    let foo = await _import('helpers', 'foo')
                    let alertProxy = await _import('alert')
                </script>
            HTML);

        // IIFE was invoked
        $browser->assertScript('window.test_evaluated', true);

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_logs_console_error_when_a_module_is_imported_using_a_different_alias()
    {
        $this->markTestSkipped("can't inspect console for thrown errors");

        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/output-to-id" as="foo" />
            <x-import module="~/output-to-id" as="bar" />
        HTML);

        // Raises console errors
        dd($browser->driver->manage()->getLog('browser'));
        $this->assertNotEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_thows_exception_when_debug_mode_enabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('app.debug', true);
        });

        $this->expectException(ViewException::class);

        $this->blade(<<< 'HTML'
            <x-import module="~/foo" as="bar" />
        HTML);
    }

    /** @test */
    public function it_throws_exception_when_debug_mode_enabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('app.debug', true);
        });

        $this->expectException(ViewException::class);

        $browser = $this->blade(<<< 'HTML'
            <x-import module="~/foo" as="bar" />
        HTML);

        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_doesnt_throw_exceptions_when_debug_mode_disabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('app.debug', false);
        });

        $this->blade(<<< 'HTML'
            <x-import module="~/foo" as="bar" />
        HTML);

        $this->assertTrue(true); // No Exceptions raised
    }

    /** @test */
    public function it_logs_console_errors_when_debug_mode_disabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('app.debug', false);
        });

        $browser = $this->blade(<<< 'HTML'
                <x-import module="~/nonexistent-module" as="foo" />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelector('script[data-bundle="~/nonexistent-module"').innerHTML
            JS, "throw 'BUNDLING ERROR: import ~/nonexistent-module as foo'");
    }
}
