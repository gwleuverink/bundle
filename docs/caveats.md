---
nav_order: 6
title: Caveats
---

## Caveats

A couple of things to be aware of.

### Tree shaking

Tree shaking is currently not supported. Keep this in mind. When a module uses named exports the `x-bundle` component will inline all of it's exports.

For example; when bundling lodash all of it's exports will be included in the bundle, regardless of if the export is used later down in your template. This effect can be mitigated by using the per-method import approach.

This might be improved when chunking dynamic imports support is added. So shared code is fetched by a additional request.

### Code splitting

Chunking of dynamicly fetched pieces of shared code is currently not supported but might be possible.

This means that if you bundle a script in your resources directory that both require on the same node_module dependency, the dependency will be bundled in both imports.

If we were able to add code splitting we would be able to chunk these shared modules in a separate file, so those chunks can load dynamically over http.

Due to Bun's path remapping behaviour Bundle is not able to split chunks from modules and assets imported from a path below it's internal project root (which is in the storage directory). If Bun fixes this issue this feature might be possible in the future.

<!-- TODO: Add a detailed treeview of chunking vs how it's done now -->
<!-- NOTE: A workaround where your local scripts also use _bundle() & we preload all dependencies in the blade template is possible. But less than ideal. -->

### Don't pass dynamic variables to `<x-bundle />`

This will work perfectly fine during development, but this can't be evaluated when compiling all your code for your production environment.

```html
<x-bundle :import="$foo" as="{% raw %}{{ $bar }}{% endraw %}" />
```

### Prevent Bundle from loading the same import multiple times

Bundle uses laravel's `@once` direcive internally, so you don't have to worry about loading the same import more than once.

### Run `view:clear` after npm updates

The title said it all. Not doing this _may_ result into issues where `<x-bundle>` serves old code.
