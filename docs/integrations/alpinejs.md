---
title: AlpineJS
parent: Integration examples
nav_order: 3
---

## Usage in AlpineJS

Using Bundle in [AlpineJS](https://alpinejs.dev) is as easy as using it in an inline script.

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
