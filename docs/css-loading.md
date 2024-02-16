---
nav_order: 5
title: CSS loading
image: "/assets/social-square.png"
---

## CSS Loading

**Beta**

Bun doesn't ship with a CSS loader. They have it on [the roadmap](https://github.com/oven-sh/bun/issues/159){:target="\_blank"} but no release date is known at this time.

We provide a custom CSS loader plugin that just works™. You only need to install [Lightning CSS](https://lightningcss.dev/), the rest is taken care of.

```bash
npm install lightningcss --save-dev
```

Now you can import `css` files. Bundle transpiles them and injects it on your page with zero effort.

```html
<x-import module="tippy.js" as="tippy" />
<x-import module="tippy.js/dist/tippy.css" />
```

### Local CSS loading

This works similar to [local modules](https://laravel-bundle.dev/local-modules.html). Simply add a new path alias to your `jsconfig.json` file.

```json
{
  "compilerOptions": {
    "paths": {
      "~/css": ["./resources/css/*"]
    }
  }
}
```

Now you can load css from your resources directory.

```html
<x-import module="css/foo-bar.css" />
```

### Browser targeting

Bundle automatically compiles many modern CSS syntax features to more compatible output that is supported in your target browsers. This includes some features that are not supported by browsers yet, like nested selectors & media queries, without using a preprocessor like Sass. [Check here](https://lightningcss.dev/transpilation.html#syntax-lowering) for the list of the many cool new syntaxes Lightning CSS supports.

You can define what browsers to target using your `package.json` file:

```json
{
  "browserslist": ["last 2 versions", ">= 1%", "IE 11"]
}
```

### Sass

In order to load `scss` files you need to install [Sass](https://sass-lang.com/) as a dependency.

```bash
npm install lightningcss --save-dev
```

Bundle will detect Sass is installed and enable bundling scss files with zero configuration.
