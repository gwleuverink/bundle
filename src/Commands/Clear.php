<?php

namespace Leuverink\Bundle\Commands;

use Illuminate\Console\Command;
use Leuverink\Bundle\Contracts\BundleManager;
use function Laravel\Prompts\info;

class Clear extends Command
{
    protected $signature = 'bundle:clear';
    protected $description = 'Clear compiled Bundle scripts';

    public function handle(BundleManager $manager): void
    {
        $manager->tempDisk()->deleteDirectory('');
        $manager->buildDisk()->deleteDirectory('');

        info('Compiled Bundle scripts cleared!');
    }
}
