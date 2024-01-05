# Bundle

Solve page specific JavaScript modules in SSR Laravel apps.

***In development - not production ready***

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

```html
<x-bundle import="~/alert" as="alert" />
```

Renders the following:

```html
<!--[BUNDLE: alert from '~/alert']-->
<script>
    var c=Object.defineProperty;var f=(w,d)=>{for(var n in d)c(w,n,{get:d[n],enumerable:!0,configurable:!0,set:(b)=>d[n]=()=>b})};var h=(w,d)=>()=>(w&&(d=w(w=0)),d);var u={};f(u,{default:()=>{{return o}}});function o(w){alert(w)}var _=h(()=>{});if(!window._bundle_modules)window._bundle_modules={};window._bundle_modules.alert=Promise.resolve().then(() => (_(),u));window._bundle=async function(w,d="default"){return(await window._bundle_modules[w])[d]};
</script>
<!--[ENDBUNDLE]>-->
```

The script exposes a global js function `_bundle` which you can use to fetch the bundled import by the name you've passed to the `as` property. The `_bundle` function accepts a optional `export` argument which defaults to 'default'.

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

### Caveats

A couple of things to be aware of;

**Tree shaking**

Tree shaking is currently not supported. Keep this in mind. When a module uses named exports the `x-bundle` component will inline all of it's exports. You may retreive those like explained above.

Because of this you may end up with a bunch of unused code imlined in your blade template. But since the code is included with the initial render this still is a lot less heavy compared to fetching all code, including unused code, from a CDN. Depending on the size of the initial request.

This might be improved whem chunking dynamic imports support is added. 

**Chunking dynamic imports**

Chunking of dynamicly fetched pieces of shared code is currently not supported but definetly possible.

Due to Bun's path remapping behaviour Bundle is not able to split chunks from modules and assets imported from your local `resources` directory. This could definetly work for shared imports from `node_modules` in the future.

**Don't pass dynamic variables to `<x-bundle/>`**

This will work perfectly fine during development, but this can't be evaluated when compiling all your code for your production environment. (feature pending, see next heading)

```html
<x-bundle :import="$foo" as="{{ $bar }}" />
```

**Running on a server**

Eventhough Bun is very fast, since Bundle transpiles & bundles your imports on the fly it might slow down your uncached blade renders a bit. Because of this it is not reccommended to run on a production server. Code should be compiled before you deploy your app.

At this time there is no command to compile all the code at once. But there will be, soonish. So stay tuned.

**Prevent Bundle from loading the same import multiple times**

Bundle uses laravel's `@once` direcive internally, so you don't have to worry about loading the same import more than once.
