---
nav_order: 3
title: Local modules
image: "/assets/social-square.png"
---

{: .note }
> Bundle is meant as a tool for Blade centric apps, like [Livewire](https://livewire.laravel.com), to enable colocation of page specific JavaScript inside Blade. Preferably the bulk your code should live inline in a script tag or in a [Alpine](https://alpinejs.dev) component.
>
> Local modules are a place to put boilerplate code. Like importing a single module & doing some setup, loading plugins etc. Writing complex code in here is discouraged. You should put that a inline script within the same component/partial that consumes it instead.

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

## Initable exports

You can use this mechanism to immediatly invoke a local module's default export to, for example, import & bootstrap other libraries.

Consider the following example file `resources/js/hello-world.js`:

```javascript
export default () => {
  alert("Hello World!");
};
```

Then in your template you can use the `<x-import />` component combined with the `init` prop to evaluate this function immediately after it loads. Without the need of calling the `_import` function manually.

```html
<!-- User will be alerted with 'Hello World' -->
<x-import module="~/hello-world" init />
```

### Bootstrapping libraries with a initable export

Initable exports can be used in a variety of creative ways. For example for swapping out Laravel's default `bootstrap.js` for an approach where you only pull in a configured library when you need it.

```javascript
import axios from "axios";

export default () => {
  window.axios = axios;

  window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
};
```

When importing this module you can omit the `as` prop. Axios will be available on the window object since the function is evaluated on import.

```html
<x-import module="~/bootstrap/axios" init />

<script type="module">
  axios
    .get("/user/12345")
    .then((response) => alert(response.data))
    .catch((error) => alert(error.message));
</script>
```

Note that your consuming script still needs to be of `type="module"` otherwise `window.axios` will be undefined.

It is also good to point out (again) that Bundle's primary goal is to get imports inside your Blade template. While the init strategy can be very powerful, it is not the place to put a lot of business code since can be a lot harder to debug.

{: .warning }
> Code splitting is [not supported](https://laravel-bundle.dev/caveats.html#code-splitting). Be careful when importing modules in your local scripts like this. When two script rely on the same dependency, it will be included in both bundles. This approach is meant to allow setup of more complex libraries. It is recommended to add complex code inside your templates instead and only use Bundle for importing libraries.

### Combining named & initable exports

The `init` prop always invokes the module's exported default function. This may be combined with other named exports. For this to work you'll need to provide both the `init` prop & `as` alias. For example, given the following script in `resources/js/hello-world.js`:

```javascript
export default () => {
  alert("Hello World!");
};

export function example() {
  //
}
```

Can be used like this:
```html
<!-- The default function will invoke immediately & alert 'Hello World!' -->
<x-import module="~/hello-world" as="hello-world" init />

<!-- Other exports may still be imported -->
<script type="module">
  const example = await _import("hello-world", "example");

  example();
</script>
```
