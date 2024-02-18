<?php

namespace Leuverink\Bundle\Tests\Browser;

use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class CssLoaderTest extends DuskTestCase
{
    /** @test */
    public function it_injects_a_style_tag_on_the_page()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/red-background.css" />
        HTML);

        // Expect CSS rendered on page
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/red-background.css"]`).innerHTML',
            'html{background:red}'
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_handles_css_files()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/red-background.css" />
        HTML);

        // Expect CSS rendered on page
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/red-background.css"]`).innerHTML',
            'html{background:red}'
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_supports_sass()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/blue-background.scss" />
        HTML);

        // Expect CSS rendered on page
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/blue-background.scss"]`).innerHTML',
            'html body{background:#00f}'
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_processes_css_imports()
    {
        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/imported-red-background.css" />
        HTML);

        // Expect CSS rendered on page
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/imported-red-background.css"]`).innerHTML',
            'html{background:red}'
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_minifies_css_when_minification_enabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('bundle.minify', true);
        });

        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/red-background.css" />
        HTML);

        // Expect CSS rendered on page
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/red-background.css"]`).innerHTML',
            'html{background:red}'
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_doesnt_minify_css_when_minification_disabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('bundle.minify', false);
        });

        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/red-background.css" />
        HTML);

        // Expect CSS rendered on page
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/red-background.css"]`).innerHTML',
            <<< 'CSS'
            html {
              background: red;
            }

            CSS
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_generates_sourcemaps_when_enabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('bundle.minify', true);
            $config->set('bundle.sourcemaps', true);
        });

        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/red-background.css" />
        HTML);

        // Assert output contains encoded sourcemap (flaky. asserting on encoded sting)
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/red-background.css"]`).innerHTML.startsWith("html{background:red}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjpudWxsLCJtYXBwaW5ncyI6IkFBQUEi")',
            true
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_doesnt_generate_sourcemaps_by_default()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('bundle.minify', true);
            $config->set('bundle.sourcemaps', false);
        });

        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/red-background.css" />
        HTML);

        $browser->assertScript(
            'document.querySelector(`style[data-module="css/red-background.css"]`).innerHTML',
            'html{background:red}'
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_generates_scss_sourcemaps_when_enabled()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('bundle.minify', true);
            $config->set('bundle.sourcemaps', true);
        });

        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/blue-background.scss" />
        HTML);

        // Assert output contains encoded sourcemap (flaky. asserting on encoded sting)
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/blue-background.scss"]`).innerHTML.startsWith("html body{background:#00f}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290Ij")',
            true
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }

    /** @test */
    public function it_doesnt_generate_scss_sourcemaps_by_default()
    {
        $this->beforeServingApplication(function ($app, $config) {
            $config->set('bundle.minify', true);
            $config->set('bundle.sourcemaps', false);
        });

        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/blue-background.scss" />
        HTML);

        $browser->assertScript(
            'document.querySelector(`style[data-module="css/blue-background.scss"]`).innerHTML',
            'html body{background:#00f}'
        );

        // Doesn't raise console errors
        $this->assertEmpty($browser->driver->manage()->getLog('browser'));
    }
}
