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
    public function it_handles_scss_files()
    {
        $this->markTestSkipped('not implemented');

        $browser = $this->blade(<<< 'HTML'
            <x-import module="css/blue-background.scss" />
        HTML);

        // Expect CSS rendered on page
        $browser->assertScript(
            'document.querySelector(`style[data-module="css/blue-background.scss"]`).innerHTML',
            'html{& body{background:#00f}}'
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
        $this->markTestSkipped('not implemented');
    }

    /** @test */
    public function it_doesnt_generate_sourcemaps_by_default()
    {
        $this->markTestSkipped('not implemented');
    }
}
