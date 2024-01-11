---
title: AlpineJS
parent: Integration examples
nav_order: 3
---

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

<br />
{: .note }

> _Note that this code serves as an example, you need more to actually integrate this library fully. See [Alpine UI Components](https://alpinejs.dev/component/choices){:target="\_blank"}_

<br />
