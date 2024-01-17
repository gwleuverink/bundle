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

<script data-bundle="alert">
  // Your minified bundle
</script>
```

## Per method exports

If a module supports per method exports, like `lodash` does, it is recomended to import the single method instead of the whole module & only retrieving the desired export later.

```html
<x-import module="lodash/filter" as="filter" />
<!-- 25kb -->
<!-- as opposed to -->
<x-import module="lodash" as="lodash" />
<!-- 78kb -->
```

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
    resource_path('views/**/*.blade.php')
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
