---
nav_order: 1
title: Quickstart
image: "/assets/social-square.png"
description: "Effortless page specific JavaScript modules in Laravel/Livewire apps"
---

[![tests](https://github.com/gwleuverink/bundle/actions/workflows/tests.yml/badge.svg)](https://github.com/gwleuverink/bundle/actions/workflows/tests.yml)
[![browser-tests](https://github.com/gwleuverink/bundle/actions/workflows/browser-tests.yml/badge.svg)](https://github.com/gwleuverink/bundle/actions/workflows/browser-tests.yml)
[![codestyle](https://github.com/gwleuverink/bundle/actions/workflows/codestyle.yml/badge.svg)](https://github.com/gwleuverink/bundle/actions/workflows/codestyle.yml)
[![codecov](https://codecov.io/github/gwleuverink/bundle/graph/badge.svg?token=ZLFQ76HKRQ)](https://codecov.io/github/gwleuverink/bundle)

{: .fs-5 .fw-300}
Laravel Blade with JavaScript Superpowers! 🚀 <br /> Effortless page specific JavaScript modules in Laravel/Livewire apps

{: .note }

> **Bundle is in open beta! 👀**
>
> We need your help get this package production ready! Check out the [discussion board](https://github.com/gwleuverink/bundle/discussions) or [report a bug](https://github.com/gwleuverink/bundle/issues/new/choose). We appreciate your feedback!

## Quickstart

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
