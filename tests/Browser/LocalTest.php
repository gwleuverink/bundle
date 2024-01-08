<?php

use Laravel\Dusk\Browser;

it('injects import & _bundle function on the window object new')
    ->blade(<<< HTML
        <x-bundle import="~/alert" as="alert" />
    HTML)
    ->assertScript('window._bundle')
    ->assertScript('window._bundle_modules');



it('imports from local resource directory')
    ->blade(<<< HTML
        <x-bundle import="~/alert" as="alert" />

        <script type="module">
            var module = await _bundle('alert');
            module('Hello World!')
        </script>
    HTML)
    ->assertDialogOpened('Hello World!');



    it('renders blade & can make assertions on the browser output')
        ->blade(<<<HTML
            <script>
                alert('Hello World!')
            </script>
        HTML)
        ->assertDialogOpened('Hello World!');
