# Bundle

Effortless page specific JavaScript modules in Laravel/Livewire apps.

- Explore the docs on **[GitHub Pages »](https://gwleuverink.github.io/bundle/)**

**_In development - not production ready_**

## Installation

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
<x-bundle import="apexcharts" as="ApexCharts" />

<script type="module">
  const ApexCharts = await _bundle("ApexCharts");

  // Create something amazing!
</script>
```

### Contributing

Clone this repo locally & run `composer install`

Run `composer serve` to start a local environment to tinker in.

You can run the test suites with the following composer scripts:

- `composer test` to run all tests except browser test
- `composer test-browser` to run all browser test
