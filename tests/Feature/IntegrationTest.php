<?php

use Leuverink\Bundle\BundleManager;
use Illuminate\Support\Facades\Blade;
use Leuverink\Bundle\Components\Import;
use Leuverink\Bundle\Exceptions\BundlingFailedException;

it('can be faked', function () {
    BundleManager::fake();

    Blade::renderComponent(new Import('~/foo', 'bar'));
});

it('transpiles JavaScript')->bundle(
    <<< 'JS'
    const foo = 'bar'
    console.log(foo)
    JS
)->transpilesTo(
    <<< 'JS'
    console.log("bar");
    JS
);

it('strips unnessary whitespace')->bundle(
    <<< 'JS'

    const foo = 'baz'
    console.log(foo)


    JS
)->transpilesTo(
    <<< 'JS'
    console.log("baz");
    JS
);

it('supports tree shaking for variables')->bundle(
    <<< 'JS'
    const foo = 'baz'
    const bar = 'zah'
    console.log(foo)
    JS
)->transpilesTo(
    <<< 'JS'
    console.log("baz");
    JS
);

it('generates sourcemaps when enabled')
    ->defer(
        fn () => config()->set('bundle.sourcemaps', true)
    )
    ->bundle(
        <<< 'JS'
        const filter = await import('~/output-to-id')
        JS
    )
    ->content()
    ->toContain('//# debugId');

it('doesnt generate sourcemaps by default')
    ->bundle(
        <<< 'JS'
        const filter = await import('~/output-to-id')
        JS
    )
    ->content()
    ->not->toContain('//# debugId');

// So the user can import their own js scripts from the resources/js dir
it('is unable to resolve local scripts by their relative path', function () {
    expect(function () {
        bundle(
            <<< 'JS'
            const filter = await import('./resources/js/output-to-id')
            JS
        );
    })->toThrow(BundlingFailedException::class);
});

it('is able to resolve local scripts when aliased in jsconfig.json', function () {
    expect(function () {
        // ~/ is aliased in jsconfig.json
        bundle(
            <<< 'JS'
            const filter = await import('~/output-to-id')
            JS
        );
    })->not->toThrow(BundlingFailedException::class);
});

it('throws a BundlingFailedException when blade component fails bundling', function () {
    config()->set('app.debug', true);
    $component = new Import('~/foo', 'bar');

    expect(fn () => $component->render())
        ->toThrow(BundlingFailedException::class);
});

it('allows for hyphens in as property', function () {
    $component = new Import('~/output-to-id', 'foo-bar');
    $component->render();
    expect(true)->toBeTrue(); // No exception was thrown
});

it('doesnt throw a BundlingFailedException when blade component fails bundling and debug mode is disabled', function () {
    config()->set('app.debug', false);
    $component = new Import('~/foo', 'bar');

    expect(fn () => $component->render())
        ->not->toThrow(BundlingFailedException::class);
});

it('logs console error when blade component fails bundling and debug mode is disabled', function () {
    config()->set('app.debug', false);
    $component = new Import('~/foo', 'bar');

    expect($component->render())
        ->toContain(
            'throw',
            "BUNDLING ERROR: No module found at path '~/foo'"
        )
        ->not->toThrow(BundlingFailedException::class);
});

it('renders scripts with type=module', function () {
    BundleManager::fake();

    expect(Blade::renderComponent(new Import('~/foo', 'bar')))
        ->toContain('type="module"');
});

it('renders inline scripts with type=module', function () {
    BundleManager::fake();

    expect(Blade::renderComponent(new Import('~/foo', 'bar', inline: true)))
        ->toContain('type="module"');
});

// Easiest way to verify minification is to check if the line count is below a certain threshold
it('minifies code when minification enabled', function () {
    $lineThreshold = 10;
    config()->set('bundle.minify', true);

    $script = Blade::renderComponent(new Import('~/output-to-id', 'foo', inline: true));

    expect(substr_count($script, "\n"))
        ->toBeLessThan($lineThreshold);
});

// Easiest way to verify minification is to check if the line count is above a certain threshold
it('doesnt minify code when minification disabled', function () {
    $lineThreshold = 10;
    config()->set('bundle.minify', false);

    $script = Blade::renderComponent(new Import('~/output-to-id', 'foo', inline: true));

    expect(substr_count($script, "\n"))
        ->toBeGreaterThan($lineThreshold);
});

it('serves bundles over http', function () {
    $js = <<< 'JS'
    const filter = await import('~/output-to-id')
    JS;

    $this->artisan('bundle:clear');
    $manager = BundleManager::new();
    $file = $manager->hash($js) . '.min.js';

    $this->get(
        route('bundle:import', $file)
    )->assertNotFound();

    $manager->bundle($js);

    $this->get(
        route('bundle:import', $file)
    )->assertOk();
});

it('serves bundles as Content-Type: application/javascript', function () {
    $js = <<< 'JS'
    const filter = await import('~/output-to-id')
    JS;

    $manager = BundleManager::new();
    $file = $manager->hash($js) . '.min.js';
    $manager->bundle($js);

    $this->get(
        route('bundle:import', $file)
    )->assertHeader('Content-Type', 'application/javascript; charset=utf-8');
});

it('serves bundles with Last-Modified headers', function () {
    $js = <<< 'JS'
    const filter = await import('~/output-to-id')
    JS;

    $manager = BundleManager::new();
    $file = $manager->hash($js) . '.min.js';
    $manager->bundle($js);

    $this->get(
        route('bundle:import', $file)
    )->assertHeader('Last-Modified');
});

it('serves bundles with configurable Cache-Control headers', function () {
    config()->set('bundle.cache_control_headers', 'foo');

    $js = <<< 'JS'
    const filter = await import('~/output-to-id')
    JS;

    $manager = BundleManager::new();
    $file = $manager->hash($js) . '.min.js';
    $manager->bundle($js);

    $this->get(
        route('bundle:import', $file)
    )->assertHeader('Cache-Control', 'foo, private'); // private is added in laravel's cache-control middleware
});

it('serves chunks over http')
    ->skip('Code splitting not implemented');

// Probably not possible. TODO: Create issue in Bun repo
// it('imports from node_modules are chunked')->todo();
// it('imports from outside node_modules are inlined (due to issue with Bun)')->todo();
// it('creates a single bundle when no imports are used')->skip('Code splitting not implemented');
// it('generates a single chunk when two sourcefiles use the same dependency')->skip('Code splitting not implemented');

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
