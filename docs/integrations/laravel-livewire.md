---
nav_order: 1
title: Laravel/Livewire
parent: Integration examples
image: "/assets/social-square-livewire.png"
---

{: .note }
[Important note](https://laravel-bundle.dev/integrations/laravel-livewire.html#usage-in-livewire) about Liveiwre's `@script` directive.

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

## Component composability

Since the `@once` directive is added internally you are safe to use these imports in multiple blade components. Only the first one will be rendered.

Because of this you are able to create Alpine/Blade components with composable JS dependencies. For example, a calendar input may include imports for both Alpine plugins & fullcalendar.js, regardless if those are used elsewhere on the page.

This opens up a whole new dimension to fully portable Blade components! But use with care. Shared dependencies [are not chunked](https://laravel-bundle.dev/caveats.html#code-splitting).

## Usage in Livewire

At this time `<x-import />` does not work with Livewire's `@script` directive. You may safely use Bundle's import component inside a top level page component that is loaded at first render. It is not safe to use inside elements that are conditionally rendered.

In Livewire context the page can consist of pieces of template that are conditionally rendered by Livewire. We wan't any script tags inside the conditional to be evaluated when it becomes visible after the initial page load. Refer to the [Livewire docs](https://livewire.laravel.com/docs/javascript#using-javascript-in-livewire-components){:target="\_blank"} for more information on why you'd might need this.

We know this is a huge shortcoming. We hope to add full `@script` support as soon as possible! Untill then you can use Bundle from your top level component safely.
