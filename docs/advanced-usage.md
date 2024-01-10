---
nav_order: 2
title: Advanced usage
---

## How it works

The `<x-bundle />` component bundles your import on the fly using [Bun](https://bun.sh) and inlines it in place inside a script tag.

The script exposes a global js function `_bundle` which you can use to fetch the bundled import by the name you've passed to the `as` property. The `_bundle` function accepts a optional `export` argument which defaults to 'default'.

If the module you're exporting uses named exports, you may resolve it like this:

```js
var module = await _bundle("~/module", "someNamedExport");
```

The `_bundle` function is async & returns a Promise. In order to use this in inline scripts you need to wrap it in a async function, or make the script tag you are using it in of `type="module"`.

Please refer to the examples below for a more detailed explanation on how the `_bundle` function can be utilized in different scenarios.

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

Consider the following example script in your `resources/js` directory:

```javascript
export default function alertProxy(message) {
  alert(message);
}
```

In order to use this script directly in your blade views, you simply need to import it using the `<x-bundle />` component.

```html
<x-bundle import="~/alert" as="alert" />

<script type="module">
  const module = await _bundle("alert");

  module("Hello World!");
</script>
```

## Per method exports

If a module supports per method exports, like `lodash` does, it is recomended to import the single method instead of the whole module & only retrieving the desired export later.

```html
<x-bundle import="lodash/filter" as="filter" />
<!-- as opposed to -->
<x-bundle import="lodash" as="lodash" />
```

### Artisan commands

There are a couple of commands at your disposal:

#### `artisan bundle:build`

Scan all your build_paths configured in `config/bundle.php` & compile all your imports.

You may configure what paths are scanned by publishing the Bundle config file and updating the `build_paths` array. Note this config option accepts an array of glob patterns.

```php
'build_paths' => [
    resource_path('views/**/*.blade.php')
]
```

#### `artisan bundle:clear`

Clear all bundled scripts
