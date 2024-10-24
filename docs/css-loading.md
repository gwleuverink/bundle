---
nav_order: 5
title: CSS loading
image: "/assets/social-square.png"
---

## CSS Loading

Bundle provides a custom CSS loader plugin for Bun that just works™. Built on top of [Lightning CSS](https://lightningcss.dev/).
You'll need to install Lightning CSS as a dependency.

Simply run `php artisan bundle:install` in your terminal. You will be prompted to select a CSS loading method. Choose `CSS`.

Afterwards you may use `x-import` to load css files directly. Bundle transpiles it and injects it on your page 🚀

```html
<x-import module="tippy.js" as="tippy" />
<x-import module="tippy.js/dist/tippy.css" />
```

<!--
BUN AUTO-INSTALL BROKEN!
This works in testing env, due to symlinking vendor directory. But in a real scenario, Bun encounters a node_modules dir up it's path and disable the auto install feature. Due to a ongoing issue this cannot be changed with any cli option
https://github.com/oven-sh/bun/issues/5783

Old documentation. Bring back when Bun fixes this issue
Because we use Bun as a runtime when processing your files there is no need to install Lightning CSS as a dependency. When Bun encounters a import that is not installed it will fall back to it's on internal [module resolution algorithm](https://bun.sh/docs/runtime/autoimport) & install the dependency on the fly.

That being said; We do recommend installing Lightning CSS in your project.

```bash
npm install lightningcss --save-dev
```
-->

### Sass

You can use Bundle to compile [Sass](https://sass-lang.com/) on the fly. You'll need to install both Sass & Lightning CSS in your project. Bundle takes care of the rest.

Simply run `php artisan bundle:install` in your terminal. You will be prompted to select a CSS loading method. Choose `Sass`.

{: .note }

> Due to a unresolved issue Bun is not able to auto-install LightningCSS & Sass on the fly. When this issue is fixed you won't have to install these dependencies yourself. Bun will automatically install them when needed 💅

### Local CSS loading

This works similarly to [local modules](https://laravel-bundle.dev/local-modules.html). Simply add a new path alias to your `jsconfig.json` file.

```json
{
  "compilerOptions": {
    "paths": {
      "~/css/*": ["./resources/css/*"]
    }
  }
}
```

Now you can load css from your resources directory.

```html
<x-import module="~/css/foo-bar.css" />
```

### Browser targeting

Bundle automatically compiles many modern CSS syntax features to more compatible output that is supported in your target browsers. This includes some features that are not supported by browsers yet, like nested selectors, custom media queries, high gamut color spaces e.t.c. Without using a preprocessor like Sass. [Check here](https://lightningcss.dev/transpilation.html#syntax-lowering) for the list of the many cool new syntaxes Lightning CSS supports.

You can define what browsers to target using your `package.json` file:

```json
{
  "browserslist": ["last 2 versions", ">= 1%", "IE 11"]
}
```

<br/>

{: .note }

> Bundle currently only supports browserslist using your `package.json` file. A dedicated `.browserslistrc` is not suppported at this time.
