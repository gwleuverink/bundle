---
nav_order: 2
title: How it works
---

## How it works

The `<x-import />` component bundles your import on the fly using [Bun](https://bun.sh){:target="\_blank"} and renders a script tag in place.

```html
<x-import module="apexcharts" as="ApexCharts" />

<!-- yields the following script -->

<script src="/x-import/e52def31336c.min.js" data-bundle="alert"></script>
```

<br />

{: .note }

> You may pass any attributes a script tag would accept, like `defer` or `async`

<br />

### The `_import()` helper function

After you use `<x-import />` somewhere in your template a global `_import` function will become available on the window object.

You can use this function to fetch the bundled import by the name you've passed to the `as` argument.

```js
var module = await _import("lodash"); // Resolves the module's default export
```

The `_import` function accepts a optional `export` argument which defaults to 'default'. When the module you're exporting uses named exports, you may resolve it like this:

```js
var module = await _import("lodash", "filter"); // Resolves a named export 'filter'
```

_In cases like this it might be advantagious to use per-method imports instead. Please refer to the [advanced usage example](/bundle/advanced-usage.html#per-method-exports)._

---

The `_import` function is async & returns a Promise. In order to use this in inline scripts you need to wrap it in a async function, or make the script tag you are using it in of `type="module"`.

Please refer to the [advanced usage examples](/bundle/advanced-usage.html) for a more detailed explanation on how the `_import` function can be utilized in different scenarios.

<br />

{: .note }

> Bundle will throw exceptions when Laravel's debug mode is enabled, but only raise console errors when it's not. Read all about running [Bundle in production](https://laravel-bundle.dev/production-builds.html) environments.

<br />
