# Bundle

Solve page specific JavaScript modules in SSR Laravel apps.

## Instalation

```bash
composer require leuverink/bundle --dev
```

```bash
npm install bun --save-dev
```

This is all you need to start using Bundle!

## Basic usage

You may bundle any node module or local script from your resources/js directory directly on the page.

```html
<!-- import your desired module -->
<x-bundle import="apexcharts" as="ApexCharts" />

<!-- a _bundle function will be exposed to retreive the module -->
<script type="module">
  const ApexCharts = await _bundle("ApexCharts");

  // Make something amazing!
</script>
```

## Path rewriting

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

In order to use this script directly in our blade views, you simply need to import it using the `<x-bundle />` component.

```html
<x-bundle import="~/alert" as="alert" />

<script type="module">
  const module = await _bundle("alert");

  module("Hello World!");
</script>
```

The `<x-bundle />` component bundles your import on the fly and inlines it in place inside a script tag.

The rendered script tag exposes a global js function `_bundle` which you can use to fetch the bundled import by the name you've passed to the `as` property. The `_bundle` function accepts a optional `export` argument which defaults to 'default'.

If the module you're exporting uses named exports, you may resolve it like this:

```js
var module = await _bundle("~/module", "someNamedExport");
```

## Usage in Livewire

You may use Bundle in conjunction with Livewire's `@script` directive. Refer to the [Livewire docs](https://livewire.laravel.com/docs/javascript#using-javascript-in-livewire-components) for more information on why you'd might need this.

```html
@script
<x-bundle import="apexcharts" as="ApexCharts" />
@endscript
```

## Usage in AlpineJS

Using Bundle in AlpineJS is as easy as using it in an inline script.

```html
<x-bundle import="apexcharts" as="ApexCharts" />

<div
  x-data="{
        init() {
            const ApexCharts = await _bundle('ApexCharts')
            let chart = new ApexCharts(this.$refs.chart, this.options)

            chart.render()

            // etc
        }
    }"
>
  <div x-ref="chart"></div>
</div>
```

_Note that this code serves as an example, you need more to actually integrate this library fully. See [Alpine UI Components](https://alpinejs.dev/component/choices)_

## Prevent Bundle from loading the same bundle multiple times

When you're not using `@script` or Livewire at all for that matter, it is reccomended to provide the optional `once` prop, so the script is not inlined multiple times when used in a loop, or otherwise in the same page.

```html
<x-bundle import="apexcharts" as="ApexCharts" once />
```
