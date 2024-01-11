---
nav_order: 3
title: Advanced usage
---

## Inline bundles

You can render the bundle inline by using the `inline` option. This saves an additional request and makes the import available immediately after the script has rendered.

You should apply this with consideration. You will save up on requests, but doing so will increase the initial page load response size.

```html
<x-bundle import="apexcharts" as="ApexCharts" inline />

<!-- yields the following script -->

<script data-bundle="alert">
  // Your minified bundle
</script>
```

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
<x-bundle import="lodash/filter" as="filter" /> <!-- 25kb -->
<!-- as opposed to -->
<x-bundle import="lodash" as="lodash" /> <!-- 78kb -->
```

### Artisan commands

There are a couple of commands at your disposal:

**artisan bundle:build**

Scan all your build_paths configured in `config/bundle.php` & compile all your imports.

You may configure what paths are scanned by publishing the Bundle config file and updating the `build_paths` array. Note this config option accepts an array of glob patterns.

```php
'build_paths' => [
    resource_path('views/**/*.blade.php')
]
```

**artisan bundle:clear**

Clear all bundled scripts.
