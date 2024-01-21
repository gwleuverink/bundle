---
nav_order: 2
title: AlpineJS
parent: Integration examples
image: "/assets/social-square-alpine.png"
---

## Bootstrapping Alpine via Bundle 🤝

Alpine can be bootstrapped with ease using a [local module](https://laravel-bundle.dev/local-modules.html).

**_Note that if you are trying out Bundle in a project that already uses Alpine there is no need for this. You can start using imports inside your components immediately, with zero config_**

First make sure you have [path rewritig](https://laravel-bundle.dev/local-modules.html) set up. Then install Alpine.

```bash
npm install alpinejs
```

Then create a new file `resources/js/bootstrap/alpine.js`

```javascript
import Alpine from "alpinejs";

export default (() => {
  window.Alpine = Alpine;

  Alpine.start();
})();
```

That's it! 🤟

```html
<x-import module="~/bootstrap/alpine" />

<div x-data="{ message: 'Hello World!' }">
  <h1 x-text="message"></h1>
</div>
```

Since the `@once` directive is added internally you are safe to use these imports in multiple blade components. Only the first one will be rendered.

Because of this you are able to create Alpine/Blade components with composable JS dependencies. For example, a calendar input may include imports for both Alpine & fullcalendar.js, regardless if those are used elsewhere on the page.

This opens up a whole new dimension to fully portable Blade components! But use with care. Shared dependencies [are not chunked](https://laravel-bundle.dev/caveats.html#code-splitting).

## Plugins

A plugin can be added with ease via your bootstrap module.

```javascript
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

Alpine.plugin(collapse);

export default (() => {
  window.Alpine = Alpine;

  Alpine.start();
})();
```

## Using modules in AlpineJS

A perfect pairing! Using Bundle in [AlpineJS](https://alpinejs.dev) is as easy as using it in an inline script.

Since Alpine's `x-init` directive is [async by default](https://alpinejs.dev/advanced/async) Alpine & Bundle work seamlessly together.

```html
<x-import module="tippy.js" as="tippy" />

<button
  x-init="
    let tippy = await _import('tippy')

    tippy($el, {
        content: 'Hello World!',
    });
  "
>
  Show tooltip
</button>
```

You can also use the `_import` function in the `x-data` object. This requires you make the funcion `_import` is invoked from async.

```html
<x-import module="tippy.js" as="tippy" />

<button
  x-data="{
    async init() {
        let tippy = await _import('tippy')

        tippy($el, {
            content: 'Hello World!',
        });
    }
}"
>
  Show tooltip
</button>
```

You can also import a module right inside a Alpine listener. This involves making the listener expression async. For example:

```html
<x-import module="sweetalert" as="swal" />

<button
  x-on:click="async () => {
        let swal = await _import('swal')
        swal('Hello world!');
    }"
>
  Trigger Sweetalert dialog
</button>
```

## Roadmap

There are a couple of cool ideas in the pipe. One of them is [backed Alpine components](https://laravel-bundle.dev/roadmap.html#backed-alpine-components). It would be incredible if this feature is possible in the future 🤞
