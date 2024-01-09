<?php

namespace Leuverink\Bundle\Commands;

use Illuminate\Console\Command;
use Leuverink\Bundle\Contracts\BundleManager;
use function Laravel\Prompts\info;

class Build extends Command
{
    protected $signature = 'bundle:build';
    protected $description = 'Scan resource directory and bundle all imports for production';

    public function handle(BundleManager $manager): void
    {
        // Find all usages of x-bundle

        // Run the Bundle manager for each each occurance

        info('Bundle compiled successfully!');
    }
}
