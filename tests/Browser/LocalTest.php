<?php

use Laravel\Dusk\Browser;

it('injects import & _bundle function on the window object')
    ->browse(fn (Browser $browser) => $browser
        ->visit('/test/local-import')
        ->assertScript('window._bundle')
        ->assertScript('window._bundle_modules')
    );


it('imports from local resource directory')
    ->browse(fn (Browser $browser) => $browser
        ->visit('/test/local-import')
        ->waitForDialog(10)
        ->assertDialogOpened('Hello World!')
    );
