---
nav_order: 5
title: CSS loading
image: "/assets/social-square.png"
---

## CSS Loading

**Beta**

Bun doesn't ship with a CSS loader. They have it on [the roadmap](https://github.com/oven-sh/bun/issues/159){:target="\_blank"} but no release date is known at this time.

We provide a custom CSS loader plugin that just works™. Built on top of [Lightning CSS](https://lightningcss.dev/). Just use the `x-import` directive to load a css file directly. Bundle transpiles them and injects it on your page with zero effort.

```html
<x-import module="tippy.js" as="tippy" />
<x-import module="tippy.js/dist/tippy.css" />
```

Because we use Bun as a runtime when processing your files there is no need to install Lightning CSS as a dependency. When Bun encounters a import that is not installed it will fall back to it's on internal [module resolution algorithm](https://bun.sh/docs/runtime/autoimport) & install the dependency on the fly.

That being said; We do recommend installing Lightning CSS in your project.

```bash
npm install lightningcss --save-dev
```

### Sass

[Sass](https://sass-lang.com/) is supported out of the box. Just like with Lightning CSS you don't have to install Sass as a dependency, but it is recommended.

```bash
npm install sass --save-dev
```

Note that compiled Sass is processed with Lightning CSS afterwards, so if you plan on only processing scss files it is recommended to install both Lightning CSS & Sass.

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

<br/>

{: .note }

> Bundle currently only supports browserslist using your `package.json` file. A dedicated `.browserslistrc` is not suppported at this time.
