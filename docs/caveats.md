---
nav_order: 4
title: Caveats
---

## Caveats

A couple of things to be aware of.

### Tree shaking

Tree shaking is currently not supported. Keep this in mind. When a module uses named exports the `x-bundle` component will inline all of it's exports. You may retreive those like explained above.

Because of this you may end up with a bunch of unused code inlined in your blade template. But since the code is included with the initial render this still is a lot less heavy compared to fetching all code, including unused code, from a CDN. Depending on the size of the initial request.

This might be improved when chunking dynamic imports support is added. So shared code is fetched by a additional request.

### Chunking dynamic imports

Chunking of dynamicly fetched pieces of shared code is currently not supported but definetly possible.

Due to Bun's path remapping behaviour Bundle is not able to split chunks from modules and assets imported from your local `resources` directory. This could definetly work for shared imports from `node_modules` in the future.

### Don't pass dynamic variables to `<x-bundle />`

This will work perfectly fine during development, but this can't be evaluated when compiling all your code for your production environment.

```html
<x-bundle :import="$foo" as="{{ $bar }}" />
```

### Running on a server

Eventhough Bun is very fast, since Bundle transpiles & bundles your imports on the fly it might slow down your uncached blade renders a bit. Because of this, and to catch bundling errors before users hit your page, it is not reccommended to run on a production server. Code should be compiled before you deploy your app.

You may run `php artisan bundle:build` to bundle all your imports beforehand. These will be added to your `storage/app/bundle` directory, make sure to add those to vsc or otherwise build them in CI before deployment.

Furthermore it is reccomended to cache your blade views on the server by running `php artisan view:cache` in your deploy script.

### Prevent Bundle from loading the same import multiple times

Bundle uses laravel's `@once` direcive internally, so you don't have to worry about loading the same import more than once.

**Run `view:clear` after npm updates**

The title said it all. Not doing this _may_ result into issues where `<x-bundle>` serves old code.
