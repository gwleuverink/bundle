<?php

namespace Leuverink\Bundle\Tests\Browser;

use PHPUnit\Framework\Attributes\Test;
use Leuverink\Bundle\Tests\DuskTestCase;

// Pest & Workbench Dusk don't play nicely together
// We need to fall back to PHPUnit syntax.

class InjectsCoreTest extends DuskTestCase
{
    #[Test]
    public function it_injects_import_and_import_function_on_the_window_object_without_using_the_import_component()
    {
        $this->blade('')
            ->assertScript('typeof window._import', 'function')
            ->assertScript('typeof window.x_import_modules', 'object');
    }
}
