---
nav_order: 1
title: Laravel/Livewire
parent: Integration examples
image: "/assets/social-square.png"
---

## Usage in plain Laravel

When using Bundle in your Blade views, you may push/prepend `<x-import />` to a stack.

Please refer to the [Laravel documentation](https://laravel.com/docs/10.x/blade#stacks){:target="\_blank"} for more information about using stacks.

```html
@stack('scripts')

<!--  -->

@push('scripts')
<x-import module="apexcharts" as="ApexCharts" />
@endstack
```

Bundle uses the `@once` directive internally, so there is no need to wrap the component in this directive yourself.

---

After you've used the `<x-import>` in your template you can retrieve the bundle inside any inline script.

```html
<script type="module">
  const ApexCharts = await _import("ApexCharts");

  // Create something amazing!
</script>
```

## Usage in Livewire

You may use Bundle in conjunction with Livewire's `@script` directive. This serves a similar purpose as using stacks in plain Laravel, but evaluates scripts even when it was appended on the page after the initial load.

Refer to the [Livewire docs](https://livewire.laravel.com/docs/javascript#using-javascript-in-livewire-components){:target="\_blank"} for more information on why you'd might need this.

```html
@script
<x-import module="apexcharts" as="ApexCharts" />
@endscript
```