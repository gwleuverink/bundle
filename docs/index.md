---
nav_order: 1
title: Quickstart
---

[![codestyle](https://github.com/media-code/workspace/actions/workflows/codestyle.yml/badge.svg)](https://github.com/media-code/workspace/actions/workflows/codestyle.yml)
[![tests](https://github.com/media-code/workspace/actions/workflows/tests.yml/badge.svg)](https://github.com/media-code/workspace/actions/workflows/tests.yml)
[![coverage](https://img.shields.io/codecov/c/github/media-code/workspace?token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/media-code/workspace)
[![core coverage](https://img.shields.io/codecov/c/github/media-code/workspace-core?label=core%20coverage&token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/media-code/workspace-core)

Effortless page specific JavaScript modules in Laravel/Livewire apps {: .fs-6 .fw-300}

## Quickstart

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
