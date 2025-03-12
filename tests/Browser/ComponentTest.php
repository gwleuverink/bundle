<?php

namespace Leuverink\Bundle\Tests\Browser;

use Illuminate\View\ViewException;
use PHPUnit\Framework\Attributes\Test;
use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

// Old syntax as a reference:

// it('renders the same import only once')
//     ->bladeString(<<< HTML
//         <x-import module="~/alert" as="alert" />
//         <x-import module="~/alert" as="alert" />
//     HTML)
//     ->assertScript(<<< JS
//         document.querySelectorAll('script[data-module="alert"').length
//     JS, 1);

class ComponentTest extends DuskTestCase
{
    #[Test]
    public function it_renders_the_same_import_only_once()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" />
                <x-import module="~/output-to-id" as="output" />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-module="~/output-to-id"').length
            JS, 1);
    }

    #[Test]
    public function it_renders_the_same_import_only_once_when_one_was_inlined()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" />
                <x-import module="~/output-to-id" as="output" inline />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-module="~/output-to-id"').length
            JS, 1);
    }

    #[Test]
    public function it_renders_multiple_imports_when_they_only_use_a_module_property()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/function-is-evaluated" />
                <x-import module="~/output-to-id" />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-module="~/function-is-evaluated"').length
            JS, 1)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-module="~/output-to-id"').length
            JS, 1);
    }

    #[Test]
    public function it_renders_the_same_import_under_different_aliases()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/output-to-id" as="foo" />
                <x-import module="~/output-to-id" as="bar" />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-module="~/output-to-id"').length
            JS, 2);
    }

    #[Test]
    public function it_renders_script_inline_when_inline_prop_was_passed()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" inline />
            HTML)
            // Assert it doesn't render src attribute on the script tag
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-module="~/output-to-id"')[0].hasAttribute('src')
            JS, false)
            // Assert script tag has content
            ->assertScript(<<< 'JS'
                typeof document.querySelectorAll('script[data-module="~/output-to-id"')[0].innerHTML
            JS, 'string');
    }

    public function it_doesnt_render_script_inline_by_default()
    {
        $this->bladeString(<<< 'HTML'
                <x-import module="~/output-to-id" as="output" />
            HTML)
            // Assert it renders src attribute on the script tag
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-module="~/output-to-id"')[0].hasAttribute('src')
            JS, true)
            // Assert script tag has no content
            ->assertScript(<<< 'JS'
                document.querySelectorAll('script[data-module="~/output-to-id"')[0].innerHTML
            JS, null);
    }

    #[Test]
    public function it_works_when_a_iife_is_combined_with_multiple_aliased_imports()
    {
        $browser = $this->bladeString(<<< 'HTML'
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

    #[Test]
    public function it_logs_console_error_when_a_module_is_imported_using_a_different_alias()
    {
        $this->markTestSkipped("can't inspect console for thrown errors");

        $browser = $this->bladeString(<<< 'HTML'
            <x-import module="~/output-to-id" as="foo" />
            <x-import module="~/output-to-id" as="bar" />
        HTML);

        // Raises console errors
        $this->assertNotEmpty($browser->driver->manage()->getLog('browser'));
    }

    #[Test]
    public function it_thows_exception_when_debug_mode_enabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('app.debug', true);
        });

        $this->expectException(ViewException::class);

        $this->bladeString(<<< 'HTML'
            <x-import module="~/foo" as="bar" />
        HTML);
    }

    #[Test]
    public function it_throws_exception_when_debug_mode_enabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('app.debug', true);
        });

        $this->expectException(ViewException::class);

        $browser = $this->bladeString(<<< 'HTML'
            <x-import module="~/foo" as="bar" />
        HTML);

        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    #[Test]
    public function it_doesnt_throw_exceptions_when_debug_mode_disabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('app.debug', false);
        });

        $this->bladeString(<<< 'HTML'
            <x-import module="~/foo" as="bar" />
        HTML);

        $this->assertTrue(true); // No Exceptions raised
    }

    #[Test]
    public function it_logs_console_errors_when_debug_mode_disabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('app.debug', false);
        });

        $browser = $this->bladeString(<<< 'HTML'
                <x-import module="~/nonexistent-module" as="foo" />
            HTML)
            ->assertScript(<<< 'JS'
                document.querySelector('script[data-module="~/nonexistent-module"').innerHTML
                    .startsWith('throw "BUNDLING ERROR: Could not resolve: "~/nonexistent-module". Maybe you need to "bun install"?')
            JS, true);
    }
}
