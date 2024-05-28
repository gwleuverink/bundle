<?php

namespace Leuverink\Bundle\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class Version extends Command
{
    protected $signature = 'bundle:version';
    protected $description = 'Lists Bundle and it\'s dependencies versions.';

    public function handle(): int
    {
        $this->components->twoColumnDetail('  <fg=green;options=bold>Back-end</>');
        $this->components->twoColumnDetail('Bundle', $this->composerPackageVersion('leuverink/bundle'));
        $this->components->twoColumnDetail('Laravel', phpversion());
        $this->components->twoColumnDetail('PHP', $this->laravel->version());

        $this->newLine();
        $this->components->twoColumnDetail('  <fg=green;options=bold>Front-end</>');
        $this->components->twoColumnDetail('Bun', $this->npmPackageVersion('bun'));
        $this->components->twoColumnDetail('LightningCSS', $this->npmPackageVersion('lightningcss'));
        $this->components->twoColumnDetail('Sass', $this->npmPackageVersion('sass'));

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
