<?php

use Laravel\Dusk\Browser;

it('renders the same import only once')
    ->blade(<<< HTML
        <x-bundle import="~/alert" as="alert" />
        <x-bundle import="~/alert" as="alert" />
    HTML)
    ->assertSee("BUNDLE: alert from '~/alert'")
    ->assertSee("SKIPPED: alert from '~/alert'");
