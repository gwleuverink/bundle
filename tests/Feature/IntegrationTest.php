<?php

use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Exceptions\BundlingFailedException;

it('transpiles JavaScript')->bundle(
    <<< JS
    const foo = 'bar'
    console.log(foo)
    JS
)->transpilesTo(
    <<< JS
    console.log("bar");
    JS
);

it('strips unnessary whitespace')->bundle(
    <<< JS

    const foo = 'baz'
    console.log(foo)


    JS
)->transpilesTo(
    <<< JS
    console.log("baz");
    JS
);


it('supports tree shaking for variables')->bundle(
    <<< JS
    const foo = 'baz'
    const bar = 'zah'
    console.log(foo)
    JS
)->transpilesTo(
    <<< JS
    console.log("baz");
    JS
);


// These two should be browser tests? or can we get the file's hash some other wayy?
test('generated bundles are reachable over http', function() {
    $js = <<< JS
    const filter = await import('~/alert')
    JS;

    $this->artisan('bundle:clear');
    $manager = BundleManager::new();
    $file = $manager->hash($js) . '.min.js';

    $this->get(
        route('x-bundle', $file)
    )->assertNotFound();

    $manager->bundle($js);

    $this->get(
        route('x-bundle', $file)
    )->assertOk();
});

test('generated chunks are reachable over http')
    ->skip('Code splitting not implemented');


it('generates sourcemaps when enabled')
    ->defer(
        fn () => config()->set('bundle.sourcemaps_enabled', true)
    )
    ->bundle(
        <<< JS
        const filter = await import('~/alert')
        JS
    )
    ->content()
    ->toContain('//# debugId');

it('doesnt generate sourcemaps by default')
    ->bundle(
        <<< JS
        const filter = await import('~/alert')
        JS
    )
    ->content()
    ->not->toContain('//# debugId');

// Probably not possible. TODO: Create issue in Bun repo
// it('imports from node_modules are chunked')->todo();
// it('imports from outside node_modules are inlined (due to issue with Bun)')->todo();


// So the user can import their own js scripts from the resources/js dir
it('is unable to resolve local scripts by their relative path', function() {
    expect(function() {
        bundle(
            <<< JS
            const filter = await import('./resources/js/alert')
            JS
        );
    })->toThrow(BundlingFailedException::class);
});

it('is able to resolve local scripts when aliased in jsconfig.json', function() {
    expect(function() {
        // ~/ is aliased in jsconfig.json
        bundle(
            <<< JS
            const filter = await import('~/alert')
            JS
        );
    })->not->toThrow(BundlingFailedException::class);
});


it('creates a single bundle when no imports are used')->skip('Code splitting not implemented');
it('generates a single chunk when two sourcefiles use the same dependency')->skip('Code splitting not implemented');


// it('supports inline synchronous imports')
//     ->todo()
//     ->bundle("import filter from 'lodash/filter'")
//     ->toContain('node_modules/lodash/filter.js');

// Probably not possible. TODO: Create issue in Bun repo
// it('supports code splitting for dynamic imports')
//     ->todo()
//     ->bundle(
//         <<< JS
//         const filter = await import('lodash/filter')
//         JS
//     )->transpilesTo(
//         <<< JS
//         var filter = await import("./x-script/chunks/filter-GWHK62RL.js");
//         JS
//     );
