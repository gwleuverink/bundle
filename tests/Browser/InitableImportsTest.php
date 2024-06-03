<?php

namespace Leuverink\Bundle\Tests\Browser;

use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class InitableImportsTest extends DuskTestCase
{
    /** @test */
    public function it_invokes_imports_with_init_prop()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/default-function" init />
            HTML)
            ->assertScript('window.test_evaluated', true);
    }

    /** @test */
    public function it_doesnt_invoke_imports_without_init_prop()
    {
        $this->blade(<<< 'HTML'
                <x-import module="~/default-function" />
            HTML)
            ->assertScript('window.test_evaluated', null);
    }

    /** @test */
    public function it_still_registers_an_aliased_module_when_the_default_export_is_invoked()
    {
        $this->markTestIncomplete('TODO: Priority!');
    }

    /** @test */
    public function it_appends_init_to_the_bundle_filename_when_import_is_invoked()
    {
        $bundle = BundleManager::new()->bundle(<<< 'JS'
            alert('Hello World!')
        JS, ['init' => true]);

        expect($bundle)->getFilename()->toContain('init');
    }

    /** @test */
    public function it_doesnt_append_init_to_the_bundle_filename_when_import_is_not_invoked()
    {
        $bundle = BundleManager::new()->bundle(<<< 'JS'
            alert('Hello World!')
        JS);

        expect($bundle)->getFilename()->not->toContain('init');
    }

    /** @test */
    public function it_raises_a_console_error_when_invokable_import_is_not_a_function()
    {
        $this->markTestIncomplete("can't inspect console for thrown errors");

        // $this->blade(<<< 'HTML'
        //         <x-import module="~/default-object" />
        //     HTML)
        //     ->assertScript('window.test_evaluated', null);
    }

    /** @test */
    public function it_raises_a_console_error_when_invokable_import_is_not_javascript_file()
    {
        $this->markTestIncomplete("can't inspect console for thrown errors");
    }
}
