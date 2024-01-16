---
nav_order: 2
title: Livewire
parent: Integration examples
image: "/assets/social-square.png"
---

## Usage in Livewire

You may use Bundle in conjunction with Livewire's `@script` directive. This serves a similar purpose as using stacks in plain Laravel, but evaluates scripts even when it was appended on the page after the initial load.

Refer to the [Livewire docs](https://livewire.laravel.com/docs/javascript#using-javascript-in-livewire-components){:target="\_blank"} for more information on why you'd might need this.

```html
@script
<x-import module="apexcharts" as="ApexCharts" />
@endscript
```
