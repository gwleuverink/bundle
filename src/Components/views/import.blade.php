<?php // @codeCoverageIgnoreStart?>

//--------------------------------------------------------------------------
// Expose x_import_modules map
//--------------------------------------------------------------------------
if (!window.x_import_modules) window.x_import_modules = {};

//--------------------------------------------------------------------------
// Import the module & push to x_import_modules
// Invoke IIFE so we can break out of execution when needed
//--------------------------------------------------------------------------
(() => {

    @if ($init)
        // Import was marked as invokable
        // Note: don't return, since we might need to still register the module
        import('{{ $module }}')
            .then(invokable => {
                if (typeof invokable.default !== 'function') {
                    throw `BUNDLING ERROR: '{{ $module }}' not invokable - default export is not a function`
                }

                try {
                    invokable.default()
                } catch (e) {
                    throw `BUNDLING ERROR: unable to invoke '{$this->module}' - '\${e}'`
                }
            })
    @endif

    // Check if module is already loaded under a different alias
    const previous = document.querySelector(`script[data-module="{{ $module }}"]`)

    // Was previously loaded & needs to be pushed to import map
    if (previous && '{{ $as }}') {
        // Throw error when previously imported under different alias. Otherwise continue
        if (previous.dataset.alias !== '{{ $as }}') {
            throw `BUNDLING ERROR: '{{ $as }}' already imported as '\${previous.dataset.alias}'`
        }
    }

    @if (str_ends_with($module, '.css') || str_ends_with($module, '.scss'))

        // Handle CSS injection
        return import('{{ $module }}').then(result => {
            window.x_inject_styles(result.default, previous)
        })

    @else

        @if ($as)

            window.x_import_modules['{{ $as }}'] = import('{{ $module }}')

        @else

            import('{{ $module }}')

        @endif

    @endif
})();

<?php // @codeCoverageIgnoreEnd?>
