---
nav_order: 1
title: Laravel/Livewire
parent: Integration examples
image: "/assets/social-square-livewire.png"
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

## Component composability

Since the `@once` directive is added internally you are safe to use these imports in multiple blade components. Only the first one will be rendered.

Because of this you are able to create Alpine/Blade components with composable JS dependencies. For example, a calendar input may include imports for both Alpine plugins & fullcalendar.js, regardless if those are used elsewhere on the page.

This opens up a whole new dimension to fully portable Blade components! But use with care. Shared dependencies [are not chunked](https://laravel-bundle.dev/caveats.html#code-splitting).

## Usage in Livewire

You may use Bundle in conjunction with Livewire's `@assets` directive. This serves a similar purpose as using stacks in plain Laravel, but evaluates scripts even when it was appended on the page after the initial load.

Refer to the [Livewire docs](https://livewire.laravel.com/docs/javascript#loading-assets){:target="\_blank"} for more information on why you'd might need this.

```html
@assets
<x-import module="apexcharts" as="ApexCharts" />
@endassets
```

## Invoking Bundle from Livewire actions 🤯

Bundle works with Livewire's [one-off JavaScript expressions](https://livewire.laravel.com/docs/actions#evaluating-one-off-javascript-expressions). This sweet feature can be combined with Bundle imports to for example, show a sweetalert after a longer running action finished.

```html
@assets
<x-import module="sweetalert" as="swal" />
@endassets

<button wire:click="submit">Go!</button>
```

```php
public function submit()
{
    // Run some long task

    $this->js(<<< JS
        let swal = await _import('swal')
        swal('Task finished!');
    JS);
}
```

Honest to god this one blew my mind for a minute.
