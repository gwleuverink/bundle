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


it('imports from node_modules are chunked')->todo();
it('imports from outside node_modules are inlined (due to issue with Bun)')->todo();


// So the user can import their own js scripts from the resources/js dir
it('supports custom node paths')->todo();
it('creates a single bundle when no imports are used')->todo();
it('generates a single chunk when two sourcefiles use the same dependency')->todo();
