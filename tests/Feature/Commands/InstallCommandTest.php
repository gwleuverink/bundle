<?php

use Illuminate\Process\PendingProcess;
use Leuverink\Bundle\Commands\Install;
use Illuminate\Support\Facades\Process;

beforeEach(function () {
    Process::fake();
});

it('follows the happy path')
    ->artisan('bundle:install')
    ->expectsQuestion('Do you want to install Bun?', true)
    ->expectsQuestion('Would you like to use CSS loading?', 'none')
    ->assertSuccessful();

it('installs Bun when selected', function () {
    $this->artisan('bundle:install')
        ->expectsQuestion('Do you want to install Bun?', true)
        ->expectsQuestion('Would you like to use CSS loading?', 'none')
        ->assertSuccessful();

    Process::assertRan(
        sprintf('npm install bun@^%s --save-dev', Install::MIN_BUN_VERSION)
    );
});

it('doesnt install Bun when not selected', function () {
    $this->artisan('bundle:install')
        ->expectsQuestion('Do you want to install Bun?', false)
        ->expectsQuestion('Would you like to use CSS loading?', 'none')
        ->assertSuccessful();

    Process::assertDidntRun('npm install bun@^1 --save-dev');
});

it('installs LightningCSS when selected', function () {
    $this->artisan('bundle:install')
        ->expectsQuestion('Do you want to install Bun?', false)
        ->expectsQuestion('Would you like to use CSS loading?', 'css')
        ->assertSuccessful();

    Process::assertRan('npm install lightningcss@^1 --save-dev');
});

it('doesnt install LightningCSS when not selected', function () {
    $this->artisan('bundle:install')
        ->expectsQuestion('Do you want to install Bun?', false)
        ->expectsQuestion('Would you like to use CSS loading?', 'none')
        ->assertSuccessful();

    Process::assertDidntRun('npm install lightningcss@^1 --save-dev');
});

it('installs Sass when selected', function () {
    $this->artisan('bundle:install')
        ->expectsQuestion('Do you want to install Bun?', false)
        ->expectsQuestion('Would you like to use CSS loading?', 'sass')
        ->assertSuccessful();

    Process::assertRan('npm install lightningcss@^1 --save-dev');
    Process::assertRan('npm install sass@^1 --save-dev');
});

it('doesnt install Sass when not selected', function () {
    $this->artisan('bundle:install')
        ->expectsQuestion('Do you want to install Bun?', false)
        ->expectsQuestion('Would you like to use CSS loading?', 'none')
        ->assertSuccessful();

    Process::assertDidntRun('npm install lightningcss@^1 --save-dev');
    Process::assertDidntRun('npm install sass@^1 --save-dev');
});

it('doesnt install anything when nothing selected', function () {
    $this->artisan('bundle:install')
        ->expectsQuestion('Do you want to install Bun?', false)
        ->expectsQuestion('Would you like to use CSS loading?', 'none')
        ->assertSuccessful();

    // Assert for absence of npm install commands
    Process::assertDidntRun(function (PendingProcess $process) {
        return str($process->command)->startsWith('npm install');
    });
});
