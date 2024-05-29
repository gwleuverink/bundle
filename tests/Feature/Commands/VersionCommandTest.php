<?php

use Illuminate\Console\Command;

it('runs without crashing')
    ->artisan('bundle:version')
    ->assertExitCode(Command::SUCCESS)
    ->expectsOutputToContain('Bundle')
    ->expectsOutputToContain('Laravel')
    ->expectsOutputToContain('PHP')
    ->expectsOutputToContain('Bun')
    ->expectsOutputToContain('LightningCSS')
    ->expectsOutputToContain('Sass');
