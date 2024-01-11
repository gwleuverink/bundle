<?php

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


it('supports inline synchronous imports')
    ->todo()
    ->bundle("import filter from 'lodash/filter'")
    ->toContain('node_modules/lodash/filter.js');


it('supports code splitting for dynamic imports')
    ->todo()
    ->bundle(
        <<< JS
        const filter = await import('lodash/filter')
        JS
    )->transpilesTo(
        <<< JS
        var filter = await import("./x-script/chunks/filter-GWHK62RL.js");
        JS
    );

// These two should be browser tests? or can we get the file's hash some other wayy?
test('generated bundles are reachable over http')->todo();
test('generated chunks are reachable over http')->skip('Code splitting not implemented');


it('generates sourcemaps when enabled')
    ->defer(
        fn () => config()->set('bundle.sourcemaps_enabled', true)
    )
    ->bundle(
        <<< JS
        const filter = await import('lodash/filter')
        JS
    )
    ->content()
    ->toContain('//# debugId');

it('doesnt generate sourcemaps by default')
    ->bundle(
        <<< JS
        const filter = await import('lodash/filter')
        JS
    )
    ->content()
    ->not->toContain('//# debugId');


it('imports from node_modules are chunked')->todo();
it('imports from outside node_modules are inlined (due to issue with Bun)')->todo();


// So the user can import their own js scripts from the resources/js dir
it('supports custom node paths')->todo();
it('creates a single bundle when no imports are used')->todo();
it('generates a single chunk when two sourcefiles use the same dependency')->todo();
