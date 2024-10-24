---
nav_order: 4
title: Advanced usage
image: "/assets/social-square.png"
---

## Inline imports

You can render the bundle inline by using the `inline` option. This saves an additional request and makes the import available immediately after the script has rendered.

You should apply this with consideration. You will save up on requests, but doing so will increase the initial page load response size.

```html
<x-import module="apexcharts" as="ApexCharts" inline />

<!-- yields the following script -->

<script type="module" data-module="apexcharts" data-alias="ApexCharts">
  // Your minified bundle
</script>
```

## Per method exports

Because there is no way to analyze the JS codepaths on your page, Bundle cannot treeshake unused exports.
You should always account for this and only import modules your really need to minimize any unused code loaded via `x-import`.

If a module supports per method exports, like `lodash` does, it is recomended to import the single method instead of the whole module & only retrieving the desired export later.

```html
<x-import module="lodash/filter" as="filter" /> <!-- 25kb -->

<!-- as opposed to -->

<x-import module="lodash" as="lodash" /> <!-- 78kb -->
```

## Global helper functions

You can import helper functions on a per-method basis or simply retreive all of them so they are available whenever you might use them. See this example for pulling in your own helper functions.

```javascript
export function foo() {
  //
}

export function bar() {
  //
}
```

```html
<x-import module="~/helpers" as="helpers" />

<script type="module">
  const foo = await _import("helpers", "foo");

  foo();
</script>
```

## Reusable options

It can come in handy to share configuration options for npm packages between blade components. Adding another layer of composability for your ui components.

Assuming [path rewriting](https://laravel-bundle.dev/local-modules.html) is set up. Say you have a configuration object you want to pull in to a component in `resources/js/charts/bar-chart-options.js`.

```javascript
export default {
  chart: {
    height: 350,
    type: "bar",
  },
  plotOptions: {
    bar: {
      borderRadius: 10,
      dataLabels: {
        position: "top",
      },
    },
  },
};
```

Then pull it in inside your component. Using AlpineJS & ApexCharts as an example.

```html
<x-import module="apexcharts" as="ApexCharts" />
<x-import module="~/charts/bar-chart-options" as="chart-options" inline />

<div
  x-init="
    const ApexCharts = await _import('ApexCharts')
    const options = await _import('chart-options')

    var chart = new ApexCharts($el, options);
    chart.render();
"
  class="w-full xl:w-2/3"
></div>
```

## Using `_import` in a script tag without `type="module"`

All previous examples have used the `_import` function within a script tag with `type='module'`. This instructs the browser to treat the containing code as a module. Practically this means that code gets it's own namespace & you can't reach for variables outside the scope of the script tag.

A script tag with `type="module"` makes your script `defer` by default, so they are loaded in parallel & executed in order. Because of this the `_import` function has your requested module available immediately. (Since they are loaded in the same order they appeared in the DOM)

This is not the case however when you use a script tag without `type="module"`. A import might still be loading while the page encounters the `_invoke()` function.

Bundle takes care of this problem by checking the internal import map by use of a non-blocking polling mechanism. So you can safely use `_import` anywhere you want. But since the importy utility is async you need to wrap the execution logic inside a async function.

It's good practice to use that async function as a listener for the `DOMContentLoaded` or window `onload` events, so all deferred scripts are loaded & executed before your callback is invoked.

```html
<x-import module="lodash/filter" as="filter" />

<script>
  document.addEventListener("DOMContentLoaded", async () => {
    let filter = await _import("filter");
  });
</script>
```

{: .warning }

> If you reassign the `window.onload` callback directly your browser will only fire the last one, since the callback is overwriten. If you want to use the `onload` event you should use a listener instead: `window.addEventListener('load', () => { /**/ })`

## Minification

All code is minified by default. This can make issues harder to debug at times. Using sourcemaps should relieve this issue. But in case you need it you can disable minification by updating the config file or via an env variable `BUNDLE_MINIFY`.

## Sourcemaps

Sourcemaps are disabled by default. You may enable this by setting `BUNDLE_SOURCEMAPS` to true in your env file or by publishing and updating the bundle config.

Sourcemaps will be generated in a separate file so this won't affect performance for the end user.

{: .note }

> If your project stored previously bundled files you need to run the [bundle:clear](https://laravel-bundle.dev/advanced-usage.html#artisan-bundleclear) command after enabling/disabling this feature.

## Cache-Control headers

You're free to tweak Cache-Control headers bundles are served with by publishing the config file and updating the `cache_control_headers` value.

Bundle also adds a Last-Modified header in addition to naming the file based on it's hashed contents. This should cover most browser caching needs out of the box.

```
Request URL: {your-domain}/x-import/e52def31336c.min.js

Last-Modified: Fri, 12 Jan 2024, 19:00:00 UTC
Cache-Control: max-age=31536000, immutable
Content-Type: application/javascript; charset=utf-8
```

## Artisan commands

There are a couple of commands at your disposal:

### bundle:build

`artisan bundle:build` Scan all your build_paths configured in `config/bundle.php` & compile all your imports.

You can configure what paths are scanned by publishing the Bundle config file and updating the `build_paths` array. Note this config option accepts an array of paths.

```php
'build_paths' => [
    resource_path('views')
]
```

### bundle:clear

`artisan bundle:clear` Clear all bundled scripts.

### bundle:version

`artisan bundle:version` Dumps Bundle's version & it's dependencies (Bun & optionally LightningCSS & Sass).

### bundle:install

`artisan bundle:install` Prompts you through installing Bun & optional CSS loader dependencies.

## Testing fake

When writing Unit or Feature tests in your application you don't need Bundle to process & serve your imports. You may use the BundleManager fake in your test setup. When testing responses your imports won't be bundled.

```php
BundleManager::fake();
```

If you'd like to use Dusk for browser testing you probably need do to run Bundle. You can assert on any script that depends on a Bundle import. In fact, that's how most of Bundle itself is tested internally.
