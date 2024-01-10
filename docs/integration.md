---
nav_order: 3
title: Integration examples
---

## Usage in plain Laravel

When using Bundle in your SSR Blade views, you may push/prepend `<x-bundle />` to a stack.

```html
@stack('scripts')

<!--  -->

@push('scripts')
<x-bundle import="apexcharts" as="ApexCharts" />
@endstack
```

Bundle uses the `@once` directive internally, so there is no need to wrap the component in this directive yourself.

## Usage in Livewire

You may use Bundle in conjunction with Livewire's `@script` directive. This serves a similar purpose as using stacks in plain Laravel, but evaluates scripts even when it was appended on the page after the initial load.

Refer to the [Livewire docs](https://livewire.laravel.com/docs/javascript#using-javascript-in-livewire-components) for more information on why you'd might need this.

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
