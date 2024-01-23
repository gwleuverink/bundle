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

If a module supports per method exports, like `lodash` does, it is recomended to import the single method instead of the whole module & only retrieving the desired export later.

```html
<x-import module="lodash/filter" as="filter" /> <!-- 25kb -->
<!-- as opposed to -->
<x-import module="lodash" as="lodash" /> <!-- 78kb -->
```

## Global helper functions

```javascript
export function foo() {
  //
}

export function bar() {
  //
}
```

```html
<x-import module="~/named-functions" as="helpers" />

<script type="module">
  const foo = await _import("helpers", "foo");

  foo();
</script>
```

## Using `_import` in a script tag without `type="module"`

All previous examples have used the `_import` function within a script tag with `type='module'`. This instructs the browser to treat the containing code as a module. Practically this means that code gets it's own namespace & you can't reach for variables outside the scope of the script tag.

A script tag with `type="module"` makes your script `defer` by default, so they are loaded in parallel & executed in order. Because of this the `_import` function has your requested module available immediately. (Since they are loaded in the same order they appeared in the DOM)

This is not the case however when you use a script tag without `type="module"`. A import might still be loading while the page encounters the `_invoke()` function.

Bundle takes care of this problem by checking the internal import map by use of a non-blocking polling mechanism. So you can safely use `_import` anywhere you want.

Since Bundle's core is included with the first `<x-import />` that you load you do have to either wrap the import inside a `DOMContentLoaded` listener or make the import inline.

```html
<x-import module="lodash/filter" as="filter" />

<script>
  document.addEventListener("DOMContentLoaded", async () => {
    let filter = await _import("filter");
  });
</script>
```

{: .note }

> We like to explore ways to inject Bundle's core on every page. This way the `_import` function does not have to be wrapped in a `DOMContentLoaded` listener. Check out our [roadmap](https://laravel-bundle.dev/roadmap.html#roadmap) to see what else we're cooking up.

## Import resolution timeout

The `_import` function uses a built-in non blocking polling mechanism in order to account for async & deferred script loading. The import resolution time may be configured milliseconds by updating the config file or via an env variable `BUNDLE_IMPORT_RESOLUTION_TIMEOUT`. This will instruct Bundle how long the `_import` function should wait untill a module is loaded.

## Minification

All code is minified by default. This can make issues harder to debug at times. Using sourcemaps should relieve this issue. But in case you need it you can disable minification by updating the config file or via an env variable `BUNDLE_MINIFY`.

## Sourcemaps

Sourcemaps are disabled by default. You may enable this by setting `BUNDLE_SOURCEMAPS_ENABLED` to true in your env file or by publishing and updating the bundle config.

Sourcemaps will be generated in a separate file so this won't affect performance for the end user.

{: .note }

> If your project stored previously bundled files you need to run the [bundle:clear](https://laravel-bundle.dev/advanced-usage.html#artisan-bundleclear) command

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

### `artisan bundle:build`

Scan all your build_paths configured in `config/bundle.php` & compile all your imports.

You may configure what paths are scanned by publishing the Bundle config file and updating the `build_paths` array. Note this config option accepts an array of paths.

```php
'build_paths' => [
    resource_path('views')
]
```

### `artisan bundle:clear`

Clear all bundled scripts.

## Testing fake

When writing Unit or Feature tests in your application you don't need Bundle to process & serve your imports.

Simply use the BundleManager fake in your test setup. Perfect for when you're asserting on responses with feature tests.

```php
BundleManager::fake();
```

When you'd like to use Dusk for browser testing you need to run Bundle in order for your tests not to blow up. Simply don't fake the BundleManager in your DuskTestCase.

## CSS Loader

Bun doesn't ship with a css loader. They have it on [the roadmap](https://github.com/oven-sh/bun/issues/159){:target="\_blank"} but no release date is known at this time. We plan to support css loading out-of-the-box as soon as Bun does!

We'd like to experiment with Bun plugin support soon. If that is released before Bun's builtin css loader does, it might be possible to write your own plugin to achieve this.