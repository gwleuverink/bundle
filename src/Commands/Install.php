<?php

namespace Leuverink\Bundle\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\note;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\progress;

class Install extends Command
{
    protected $signature = 'bundle:install';
    protected $description = 'Installs Bun, LightningCSS & Sass';

    protected $installCommands = [];

    public function handle(): int
    {
        $this->callSilent('bundle:clear');

        $this->printAscii();
        $this->printIntro();
        $this->promptToInstallBun();
        $this->promptToInstallCssLoading();
        $this->install();

        $this->call('bundle:version');

        $this->printOutro();

        return static::SUCCESS;
    }

    protected function printAscii()
    {
        note(<<< TEXT
              ____   __  __ _   __ ____   __     ______
             / __ ) / / / // | / // __ \ / /    / ____/
            / __  |/ / / //  |/ // / / // /    / __/
           / /_/ // /_/ // /|  // /_/ // /___ / /___
          /_____/ \____//_/ |_//_____//_____//_____/
        TEXT);

        $this->newLine();
    }

    protected function printIntro()
    {
        intro(PHP_EOL . '  Thank you for installing Bundle! This wizard will set everything up for you.  ' . PHP_EOL);
    }

    protected function promptToInstallBun()
    {
        $confirmed = confirm(
            default: true,
            label: 'Do you want to install Bun?',
            hint: 'Bun needs to be installed in order to use Bundle. Skip this if you\'ve installed it manually.'
        );

        if ($confirmed) {
            $this->installCommands[] = 'npm install bun@^1 --save-dev';
        }
    }

    protected function promptToInstallCssLoading()
    {
        $choice = select(
            label: 'Would you like to use CSS loading?',
            options: [
                'css' => 'CSS (installs LightningCSS)',
                'sass' => 'Sass (installs Sass & LightningCSS)',
                'none' => 'None',
            ],
            default: 'css',
        );

        match ($choice) {
            'css' => $this->installCommands = array_merge($this->installCommands, [
                'npm install lightningcss@^1 --save-dev',
            ]),
            'sass' => $this->installCommands = array_merge($this->installCommands, [
                'npm install lightningcss@^1 --save-dev',
                'npm install sass@^1 --save-dev',
            ]),
            default => null,
        };
    }

    protected function install()
    {
        if (empty($this->installCommands)) {
            warning('Nothing to install.');

            return;
        }

        progress(
            label: 'Installing dependencies.',
            steps: $this->installCommands,
            callback: function ($command, $progress) {
                $progress->hint($command);

                Process::run($command);
            },
            hint: 'This may take some time.',
        );
    }

    protected function printOutro()
    {
        outro(PHP_EOL . "  You're all set! Check out https://laravel-bundle.dev/ to get started!  " . PHP_EOL);
    }
}
