---
title: Livewire
parent: Integration examples
nav_order: 2
---

## Usage in Livewire

You may use Bundle in conjunction with Livewire's `@script` directive. This serves a similar purpose as using stacks in plain Laravel, but evaluates scripts even when it was appended on the page after the initial load.

Refer to the [Livewire docs](https://livewire.laravel.com/docs/javascript#using-javascript-in-livewire-components) for more information on why you'd might need this.

```html
@script
<x-bundle import="apexcharts" as="ApexCharts" />
@endscript
```
