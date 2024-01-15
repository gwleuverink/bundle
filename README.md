# `x-import`

Effortless page specific JavaScript modules in Laravel/Livewire apps.

- Explore the docs on **[GitHub Pages »](https://gwleuverink.github.io/bundle/)**

**_In development - not production ready_**

[![tests](https://github.com/gwleuverink/bundle/actions/workflows/tests.yml/badge.svg)](https://github.com/gwleuverink/bundle/actions/workflows/tests.yml)
[![browser-tests](https://github.com/gwleuverink/bundle/actions/workflows/browser-tests.yml/badge.svg)](https://github.com/gwleuverink/bundle/actions/workflows/browser-tests.yml)
[![codestyle](https://github.com/gwleuverink/bundle/actions/workflows/codestyle.yml/badge.svg)](https://github.com/gwleuverink/bundle/actions/workflows/codestyle.yml)
[![coverage](https://img.shields.io/codecov/c/github/gwleuverink/bundle?token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/gwleuverink/bundle)

## Installation

```bash
composer require leuverink/bundle
```

```bash
npm install bun --save-dev
```

This is all you need to start using Bundle!

## Basic usage

You may bundle any `node_module` or local script from your `resources/js` directory directly on the page.

```html
<x-import module="apexcharts" as="ApexCharts" />

<script type="module">
  const ApexCharts = await _import("ApexCharts");

  // Create something amazing!
</script>
```

### Contributing

Clone this repo locally & run `composer install`

Run `composer serve` to start a local environment to tinker in.

You can run the test suites with the following composer scripts:

- `composer test` to run all tests except browser test
- `composer test-browser` to run all browser test
