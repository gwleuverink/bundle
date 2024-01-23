---
nav_order: 2
title: How it works
image: "/assets/social-square.png"
---

## How it works

Bundle facilitates JavaScript imports inside Blade using [Bun](https://bun.sh){:target="\_blank"}. 

Bun does all the heavy lifting. Bundle provides the glue between Blade and Bun and injects your imports on the client side.

The <x-import /> component bundles your import on the fly and renders a script tag in place.

```html
<x-import module="apexcharts" as="ApexCharts" />

<!-- yields the following script -->

<script src="/x-import/e52def31336c.min.js" type="module" data-module="alert" data-alias="ApexCharts"></script>
```

### In depth

In contrary to how an entire JavaScript app would be bundled at once, this package creates tiny bundles based on the props you pass to the `<x-import />` component.

Bun treats these bundles as being separate builds. This normally would cause collisions with reused tokens inside the window scope but this is countered by loading those bundles via a script tag with `type="module"`. This constraints the code to it's own scope and makes the script be deferred automatically.

When you use the `<x-import />` component Bundle constructs a small JS script containing your desired module & a tiny bit of code to expose the module on the page. It then bundles the entire thing up and caches it in the `storage/bundle` directory. This is then either served over http or rendered inline.

Being this constrained and relying on Bun for al the heavy lifting allows Bundle's code to be extremely thin.

<br />

<!--
{: .note }
> You may pass any attributes a script tag would accept, like `defer` or `async`. Note that scripts with `type="module"` are deferred by default.
-->

<br />

## The `_import` helper function

After you use `<x-import />` somewhere in your template a global `_import` function will become available on the window object.

You can use this function to fetch the bundled import by the name you've passed to the `as` argument.

```js
var module = await _import("lodash"); // Resolves the module's default export
```

The `_import` function accepts a optional `export` argument which defaults to 'default'. When the module you're exporting uses named exports, you may resolve it like this:

```js
var module = await _import("lodash", "filter"); // Resolves a named export 'filter'
```

_In cases like this it might be advantagious to use per-method imports instead. Please refer to the [advanced usage example](https://laravel-bundle.dev/advanced-usage.html#per-method-exports)._

---

The `_import` function is async & returns a Promise. In order to use this in inline scripts you need to wrap it in a async function, or make the script tag you are using it in of `type="module"`.

It's recommended to use a inline script of type `module`. This makes it deferred by default & instructs the browser to run those tags sequentially. If you use a script without the `module` type you can still use Bundle, but with some extra boilerplate. [Check here](https://laravel-bundle.dev/advanced-usage.html#using-_import-in-a-script-tag-without-typemodule) if you'd like to learn more

Refer to the [local modules](https://laravel-bundle.dev/local-modules.html) docs for a more detailed explanation on how the `_import` function can be utilized in different scenarios.

<br />

{: .note }

> Bundle will throw exceptions when Laravel's debug mode is enabled, but only raise console errors when it's not. Read all about running [Bundle in production](https://laravel-bundle.dev/production-builds.html) environments.

<br />
