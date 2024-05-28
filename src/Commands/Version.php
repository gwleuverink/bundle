<?php

namespace Leuverink\Bundle\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\table;

class Version extends Command
{
    protected $signature = 'bundle:version';
    protected $description = 'Lists Bundle and it\'s dependencies versions.';

    public function handle(): int
    {
        table([], rows: [
            ['Bundle:', $this->composerPackageVersion('leuverink/bundle')],
            ['Bun:', $this->npmPackageVersion('bun')],
            ['LightningCSS:', $this->npmPackageVersion('lightningcss')],
            ['Sass:', $this->npmPackageVersion('sass')],
        ]);

        return static::SUCCESS;
    }

    protected function composerPackageVersion(string $name): string
    {
        $packageInfo = json_decode(Process::run("composer show {$name} --format=json")->output());
        $versions = data_get($packageInfo, 'versions', ['Not installed']);

        return head($versions);
    }

    protected function npmPackageVersion(string $name): string
    {
        $packageInfo = json_decode(Process::run("npm list {$name} --json")->output());
        return data_get($packageInfo, "dependencies.{$name}.version", 'Not installed');
    }
}
