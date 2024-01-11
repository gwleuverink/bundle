---
title: Laravel
parent: Integration examples
nav_order: 1
---

## Usage in plain Laravel

When using Bundle in your Blade views, you may push/prepend `<x-bundle />` to a stack.

Please refer to the [Laravel documentation](https://laravel.com/docs/10.x/blade#stacks){:target="\_blank"} for more information about using stacks.

```html
@stack('scripts')

<!--  -->

@push('scripts')
<x-bundle import="apexcharts" as="ApexCharts" />
@endstack
```

Bundle uses the `@once` directive internally, so there is no need to wrap the component in this directive yourself.
