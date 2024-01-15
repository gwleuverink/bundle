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
    <a href="https://codecov.io/gh/gwleuverink/bundle" rel="nofollow"><img src="https://camo.githubusercontent.com/ba087bb1f5fdb832986038ba182a1627963a82b75907d05cc9c7f23192e8ea6e/68747470733a2f2f696d672e736869656c64732e696f2f636f6465636f762f632f6769746875622f67776c6575766572696e6b2f62756e646c653f746f6b656e3d4f4e344d54593843314226636f6c6f723d34352532433139302532433635" alt="coverage" data-canonical-src="https://img.shields.io/codecov/c/github/gwleuverink/bundle?token=ON4MTY8C1B&amp;color=45%2C190%2C65" style="max-width: 100%;"></a>
</p>

<br />

Explore the docs on **[GitHub Pages »](https://laravel-bundle.dev/)**

**_In development - not production ready_**

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

- `composer test` to run all tests except browser tests
- `composer test-browser` to run all browser tests
- `composer test-all` to run all tests
