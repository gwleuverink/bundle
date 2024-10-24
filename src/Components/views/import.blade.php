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

    <?php if ($init) { ?>
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
                    throw `BUNDLING ERROR: unable to invoke '{{ $module }}' - '\${e}'`
                }
            })
    <?php } ?>

    <?php if ($as) { ?>

        // Import should be registered under an alias. Check if module is already loaded under a different alias
        const previous = document.querySelector(`script[data-module="{{ $module }}"]`)

        // Was previously loaded & needs to be pushed to import map
        if (previous) {
            // Throw error when previously imported under different alias. Otherwise continue
            if (previous.dataset.alias !== '{{ $as }}') {
                throw `BUNDLING ERROR: '{{ $as }}' already imported as '\${previous.dataset.alias}'`
            }
        }

    <?php } ?>

    <?php if (str_ends_with($module, '.css') || str_ends_with($module, '.scss')) { ?>

        // Handle CSS injection
        return import('{{ $module }}').then(result => {
            let scriptTag = document.querySelector(`script[data-module="{{ $module }}"]`)
            window.x_inject_styles(result.default, scriptTag)
        })

    <?php } else { ?>

        <?php if ($as) { ?>

            // Register under alias
            window.x_import_modules['{{ $as }}'] = import('{{ $module }}')

        <?php } elseif (! $init) { ?>

            // No alias & no init. Simply import without handling
            import('{{ $module }}')

        <?php } ?>

    <?php } ?>
})();

<?php // @codeCoverageIgnoreEnd?>
