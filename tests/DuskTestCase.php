<?php

namespace Leuverink\Bundle\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Dusk\TestCase  as BaseTestCase;

class DuskTestCase extends BaseTestCase
{
    use WithWorkbench;
}
