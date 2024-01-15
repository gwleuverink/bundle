---
nav_order: 3
title: Advanced usage
---

## Inline bundles

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
<x-import module="lodash/filter" as="filter" /> <!-- 25kb -->
<!-- as opposed to -->
<x-import module="lodash" as="lodash" /> <!-- 78kb -->
```

## Path rewriting for local modules

If you want to import modules from any other directory than `node_modules`, you may add a `jsconfig.json` file to your project root with all your path aliases.

```json
{
  "compilerOptions": {
    "paths": {
      "~/*": ["./resources/js/*"]
    }
  }
}
```

Consider the following example script `resources/js/alert.js`:

```javascript
export default function alertProxy(message) {
  alert(message);
}
```

In order to use this script directly in your blade views, you simply need to import it using the `<x-import />` component.

```html
<x-import module="~/alert" as="alert" />

<script type="module">
  const module = await _import("alert");

  module("Hello World!");
</script>
```

## Self evaluating exports

You can use this mechanism to immediatly execute some code or to bootstrap & import other libraries.

Consider the following example the following file `resources/js/immediately-invoked.js`

```javascript
export default (() => {
  alert('Hello World!)
})();
```

Then in your template you can use the `<x-import />` component to evaluate this function. Without the need of calling the `_import()` function. Note this only works with a [IIFE](https://developer.mozilla.org/en-US/docs/Glossary/IIFE){:target="\_blank"}

```html
<!-- User will be alerted with 'Hello World' -->
<x-import module="~/immediately-invoked" />
```

This can be used in a variety of creative ways. For example for swapping out Laravel's default `bootstrap.js` for an approach where you only pull in a configured library when you need it.

```javascript
import axios from "axios";

export default (() => {
  window.axios = axios;

  window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
})();
```

```html
<x-import module="~/bootstrap/axios" as="axios" />

<script type="module">
  // axios available, since it was attached to the window inside the IIFE
  axios.get("/user/12345").then(function (response) {
    console.log(response);
  });
</script>
```

Note that your consuming script still needs to be of `type="module"` otherwise `window.axios` will be undefined.

{: .warning }

> Code splitting is [not supported](https://laravel-bundle.dev/caveats.html#code-splitting). Be careful when importing modules in your local scripts like this. When two script rely on the same dependency, it will be included in both bundles. This approach is meant to be used as a method to allow setup of more complex libraries. It is recommended to place business level code inside your templates instead.

Please note Bundle's primary goal is to get imports inside your Blade template. While the IIFE strategy can be very powerful, it is not the place to put a lot of business code since can be a lot harder to debug.

{: .note }

Bundle is meant as a tool for Blade centric apps, like Livewire, to enable code colocation with page specific JavaScript. Preferably the bulk of custom code should live inline in a script tag or in a Alpine component.

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

## Testing utilities

**This feature is pending**

When writing Unit or Feature tests in your application you don't need Bundle to process & serve your imports.

Simply use the BundleManager fake in your test setup. Perfect for when you're asserting on responses with feature tests.

```php
BundleManager::fake();
```

When you'd like to use Dusk for browser testing you need to run Bundle in order for your tests not to blow up. Simply don't fake the BundleManager in your DuskTestCase.
