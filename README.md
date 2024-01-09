# Bundle

Effortless page specific JavaScript modules in Laravel/Livewire apps.

**_In development - not production ready_**

## Instalation

```bash
composer require leuverink/bundle --dev
```

```bash
npm install bun --save-dev
```

This is all you need to start using Bundle!

## Basic usage

You may bundle any `node_module` or local script from your `resources/js` directory directly on the page.

```html
<!-- import your desired module -->
<x-bundle import="apexcharts" as="ApexCharts" />

<!-- a _bundle function will be exposed to retrieve the module -->
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

In order to use this script directly in your blade views, you simply need to import it using the `<x-bundle />` component.

```html
<x-bundle import="~/alert" as="alert" />

<script type="module">
  const module = await _bundle("alert");

  module("Hello World!");
</script>
```

## How it works

The `<x-bundle />` component bundles your import on the fly using [Bun](https://bun.sh) and inlines it in place inside a script tag.

The script exposes a global js function `_bundle` which you can use to fetch the bundled import by the name you've passed to the `as` property. The `_bundle` function accepts a optional `export` argument which defaults to 'default'.

If the module you're exporting uses named exports, you may resolve it like this:

```js
var module = await _bundle("~/module", "someNamedExport");
```

The `_bundle` function is async & returns a Promise. In order to use this in inline scripts you need to wrap it in a async function, or make the script tag you are using it in of `type="module"`.

If a module supports per method exports, like `lodash` does, it is recomended to import the single method instead of the whole module & only retrieving the desired export later.

```html
<x-bundle import="lodash/filter" as="filter" />
<!-- as opposed to -->
<x-bundle import="lodash" as="lodash" />
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

Remember that the `_bundle()` function is async, so in order to use it inside your x-data init method you should make the init method async.

You may also use the `x-init` directive. But x-init is evaluated async already, so you can just use `_bundle()` in there. No extra work required.

```html
<x-bundle import="apexcharts" as="ApexCharts" />

<div
  x-data="{
        async init() {
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

### Caveats

A couple of things to be aware of;

**Tree shaking**

Tree shaking is currently not supported. Keep this in mind. When a module uses named exports the `x-bundle` component will inline all of it's exports. You may retreive those like explained above.

Because of this you may end up with a bunch of unused code inlined in your blade template. But since the code is included with the initial render this still is a lot less heavy compared to fetching all code, including unused code, from a CDN. Depending on the size of the initial request.

This might be improved when chunking dynamic imports support is added. So shared code is fetched by a additional request.

**Chunking dynamic imports**

Chunking of dynamicly fetched pieces of shared code is currently not supported but definetly possible.

Due to Bun's path remapping behaviour Bundle is not able to split chunks from modules and assets imported from your local `resources` directory. This could definetly work for shared imports from `node_modules` in the future.

**Don't pass dynamic variables to `<x-bundle/>`**

This will work perfectly fine during development, but this can't be evaluated when compiling all your code for your production environment.

```html
<x-bundle :import="$foo" as="{{ $bar }}" />
```

**Running on a server**

Eventhough Bun is very fast, since Bundle transpiles & bundles your imports on the fly it might slow down your uncached blade renders a bit. Because of this, and to catch bundling errors before users hit your page, it is not reccommended to run on a production server. Code should be compiled before you deploy your app.

You may run `php artisan bundle:build` to bundle all your imports beforehand. These will be added to your `storage/app/bundle` directory, make sure to add those to vsc or otherwise build them in CI before deployment.

**Prevent Bundle from loading the same import multiple times**

Bundle uses laravel's `@once` direcive internally, so you don't have to worry about loading the same import more than once.

**Run `view:clear` after npm updates**

The title said it all. Not doing this _may_ result into issues where `<x-bundle>` serves old code.

### Artisan commands

There are a couple of commands at your disposal:

- `artisan bundle:build` to scan all your build_paths configured in `config/bundle.php` & compile all your imports.
- `artisan bundle:clear` to clear all bundled scripts

### Contributing

Clone this repo locally & run `composer install`

Run `composer serve` to start a local environment to tinker in.

You can run the test suites with the following composer scripts:

- `composer test` to run all tests except browser test
- `composer test-browser` to run all browser test
