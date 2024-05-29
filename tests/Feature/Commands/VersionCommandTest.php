<?php

it('runs without crashing')
    ->artisan('bundle:version')
    ->expectsOutputToContain('Bundle')
    ->expectsOutputToContain('Laravel')
    ->expectsOutputToContain('PHP')
    ->expectsOutputToContain('Bun')
    ->expectsOutputToContain('LightningCSS')
    ->expectsOutputToContain('Sass')
    ->assertSuccessful();
