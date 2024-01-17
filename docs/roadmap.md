---
nav_order: 7
title: Roadmap
image: "/assets/social-square.png"
---

## Roadmap

Bundle is under active development. If you feel there are features missing or you've got a great idea that's not on on the roadmap please [open a discussion](https://github.com/gwleuverink/bundle/discussions/categories/ideas){:target="\_blank"} on GitHub.

### Initable exports

When importing a local module, the only method to immidiatly invoke some code is by using the [self evaluating export](https://laravel-bundle.dev/advanced-usage.html#self-evaluating-exports) method.

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

### Better exception messages

We need better exception messages & add more problem solutions
