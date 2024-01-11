---
nav_order: 2
title: Introduction
---

## How it works

The `<x-bundle />` component bundles your import on the fly using [Bun](https://bun.sh) and renders a script tag in place.

```html
<x-bundle import="apexcharts" as="ApexCharts" />

<!-- yields the following script -->

<script src="/x-bundle/e52def31336c.min.js" data-bundle="alert"></script>
```

<br />

{: .note }

> You may pass any attributes a script source accepts, like `defer` or `async`.

<br />

After you use `<x-bundle />` somewhere in your template a global function `_bundle` will become available on the window object.

You can use this function to fetch the bundled import by the name you've passed to the `as` argument. The `_bundle` function accepts a optional `export` argument which defaults to 'default'.

If the module you're exporting uses named exports, you may resolve it like this:

```js
var module = await _bundle("~/module", "someNamedExport");
```

The `_bundle` function is async & returns a Promise. In order to use this in inline scripts you need to wrap it in a async function, or make the script tag you are using it in of `type="module"`.

Please refer to the examples below for a more detailed explanation on how the `_bundle` function can be utilized in different scenarios.
