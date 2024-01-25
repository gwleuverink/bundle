<p align="center" style="margin-bottom: 2rem">
    <img width="460" src="https://laravel-bundle.dev/assets/logo.svg">
</p>

<p align="center">
    Effortless page specific JavaScript modules in Laravel/Livewire apps.
</p>

<p align="center">
    <a href="https://github.com/gwleuverink/bundle/actions/workflows/tests.yml"><img src="https://github.com/gwleuverink/bundle/actions/workflows/tests.yml/badge.svg" alt="tests" style="max-width: 100%;"></a>
    <a href="https://github.com/gwleuverink/bundle/actions/workflows/browser-tests.yml"><img src="https://github.com/gwleuverink/bundle/actions/workflows/browser-tests.yml/badge.svg" alt="browser-tests" style="max-width: 100%;"></a>
    <a href="https://github.com/gwleuverink/bundle/actions/workflows/codestyle.yml"><img src="https://github.com/gwleuverink/bundle/actions/workflows/codestyle.yml/badge.svg" alt="codestyle" style="max-width: 100%;"></a>
    <a href="https://codecov.io/github/gwleuverink/bundle"><img src="https://codecov.io/github/gwleuverink/bundle/graph/badge.svg?token=ZLFQ76HKRQ"/></a>
    <a href="https://phpsandbox.io/n/uqpld"><img src="https://phpsandbox.io/img/brand/badge.png" height="20" alt="Bundle Sandbox"></a>
</p>

<br />

Explore the docs on **[GitHub Pages »](https://laravel-bundle.dev/)**

> **Bundle is in open beta! 👀**
>
> We need your help get this package production ready 🚀 Check out the [discussion board](https://github.com/gwleuverink/bundle/discussions) or [report a bug](https://github.com/gwleuverink/bundle/issues/new/choose). We appreciate your feedback!

## Installation

```bash
composer require leuverink/bundle
```

```bash
npm install bun --save-dev
```

This is all you need to start using Bundle!

## Basic usage

You may import any `node_module` or local module from your `resources/js` directory directly on the page.

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

- `composer test` to run all tests except browser tests
- `composer test-browser` to run all browser tests
- `composer test-all` to run all tests
