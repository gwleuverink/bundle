# Bundle

Solve page specific JavaScript modules in SSR Laravel apps.

## Instalation

```bash
composer require leuverink/bundle --dev
```

```bash
npm install bun --save-dev
```

Finally, add a `jsconfig.json` file to your project root with all your path aliases. (**required**)

```json
{
  "compilerOptions": {
    "paths": {
      "~/*": ["./resources/js/*"]
    }
  }
}
```

## Basic usage

**NOTE**: Working document. Api is not final

Use in inline scripts

```html
<script type="javascript">
  const _filter = @bundle('lodash/filter')
</script>
```

Or prefetch for reuse

```html
@prefetchBundle('lodash/filter')

<script type="javascript">
  const _filter = _bundle('lodash/filter')
</script>
```

In an alpine component

```html
<input
  x-data="{
        value: ['2022/01/01', '2022/01/10'],
    }"
  x-init="
        const flatpickr = @bundle('flatpickr');

        flatpickr($el, {
            mode: 'range',
            defaultDate: value,
            onChange: (date, dateString) => {
                value = dateString.split(' to ')
            }
        })
    "
/>
```

The @bundle directive fetches the bundle lazily whith an async import, thus returns a promise. Take this into consideration when using bundle inside async functions. You may want to chain on the resolved promise instead.

Alternitavely you may inline the import in place, although this is discouraged in most situations

`@bundle('lodash/filter', inline: true)`
