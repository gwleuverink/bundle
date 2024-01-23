---
nav_order: 8
title: Roadmap
image: "/assets/social-square.png"
---

## Roadmap

Bundle is under active development. If you feel there are features missing or you've got a great idea that's not on on the roadmap please [open a discussion](https://github.com/gwleuverink/bundle/discussions/categories/ideas){:target="\_blank"} on GitHub.

## CSS loader

Bun doesn't ship with a css loader. They have it on [the roadmap](https://github.com/oven-sh/bun/issues/159){:target="\_blank"} but no release date is known at this time. We plan to support css loading out-of-the-box as soon as Bun does!

Plugin support is a feature we'd like to experiment with. If that is released before Bun's builtin css loader does, it might be possible to write your own plugin to achieve this.

## Bun plugin support

We'd like to add 3rd party plugin support and, in the spirit of making things even more meta than they already are, also try to support custom plugins right from inside your `resources` directory.

## Initable exports

When importing a local module, the only method to immidiatly invoke some code is by using the [IIFE export](https://laravel-bundle.dev/local-modules.html#iife-exports) method.

An alternative API is possible that would make it a bit easier to structure your code.
Consider the following example script in `resources/js/some-module.js`. (needs a jsconfig.json for path remapping)

```javascript
export default {
  // Some properties here

  init: function () {
    // What will be executed immediately
  },

  // Some methods here
};
```

By using `init` on the import component you'll instruct Bundle to run that method immidiatly. You don't need a `as` alias in this case.

```html
<x-import module="~/some-module" init />
```

This approach will also make it possible to use named exports in combination with a init function.

```javascript
export default {
  // Some properties here

  init: function () {
    // What will be executed immediately
  },

  // Some methods here
};

export function someFunction() {
  // Do something
}
```

In this example you do need the `as` prop, since in addition to running the init function you are also retrieving a named import.

```html
<x-import module="~/some-module" as="foo" init />

<script type="module">
  const someFunction = await _import("foo", "someFunction");

  //
</script>
```

## Backed Alpine components

This would be a nice feature, but impossible at this time. I would like to implement something like this at some point, if even possible.

Consider the following simple toggle component in `resources/components/toggle`.

```javascript
export default {
  open: false,

  toggle() {
    this.open = !this.open;
  },
};
```

It would be incredible if this object could be forwarded to Alpine directly like so.

```html
<x-import module="~/bootstrap/alpine" />
<x-import module="~/components/toggle" as="alpine:toggle" />

<div x-data="await _import('alpine:toggle')">
  <button x-on:click="toggle()">Expand</button>

  <div x-show="open">Content...</div>
</div>
```

## Injecting Bundle's core on every page

This will reduce every import's size slightly. And more importantly; it will remove the need to wrap `_import` calls inside script tags without `type="module"`, making things easier for the developer and greatly decrease the chance of unexpected behaviour caused by race conditions due to slow network speeds when a `DOMContentLoaded` listener was forgotten.

## Better exception messages

We need better exception messages & add more problem solutions. There are a lot of different scenarios to account for and we need your help! If you think we should provide a Ignition solution for a specific error please reach out via [GitHub](https://github.com/gwleuverink/bundle).
