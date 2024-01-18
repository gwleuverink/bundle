---
nav_order: 2
title: AlpineJS
parent: Integration examples
image: "/assets/social-square.png"
---

## Bootstrapping Alpine via Bundle 🤝

Alpine can be bootstrapped with ease using a [local module](https://laravel-bundle.dev/local-modules.html).

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

Because of this you are able to create composable Alpine/Blade components. For example, a calendar input may include imports for both Alpine & fullcalendar.js, regardless if those are used elsewhere on the page.

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
<x-import module="tippy.js" as="tippy" defer />

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
<x-import module="tippy.js" as="tippy" defer />

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

## Backed components

It's possible to ship larger Alpine components using [Alpine.data](https://alpinejs.dev/globals/alpine-data). Simply export an [IIFE](https://laravel-bundle.dev/local-modules.html#iife-exports) containing a Alpine data definition.

```javascript
export default (() => {
  Alpine.data("dropdown", () => ({
    open: false,

    toggle() {
      this.open = !this.open;
    },
  }));
})();
```

```html
<x-import module="~/components/hello-world" />

<div x-data="dropdown">
  <button x-on:click="toggle">Open</button>

  <div x-show="open">...</div>
</div>
```

A nicer API for this would be to be able to simply feed an object to `x-data` as a default export via `_import()`, but this isn't possible at this time.
