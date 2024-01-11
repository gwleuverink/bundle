---
title: Laravel
parent: Integration examples
nav_order: 1
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
