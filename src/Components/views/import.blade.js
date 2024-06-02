//--------------------------------------------------------------------------
// Expose x_import_modules map
//--------------------------------------------------------------------------
if(!window.x_import_modules) window.x_import_modules = {};

//--------------------------------------------------------------------------
// Import the module & push to x_import_modules
// Invoke IIFE so we can break out of execution when needed
//--------------------------------------------------------------------------
(() => {

    // Import was marked as invokable
    if('{{ $init }}') {

        return import('{{ $module }}')
            .then(invokable => {
                if(typeof invokable.default !== 'function') {
                    throw `BUNDLING ERROR: '{{ $module }}' not invokable - default export is not a function`
                }

                try {
                    invokable.default()
                } catch(e) {
                    throw `BUNDLING ERROR: unable to invoke '{{ $module }}' - '\${e}'`
                }
            })
    }

    // Check if module is already loaded under a different alias
    const previous = document.querySelector(`script[data-module="{{ $module }}"]`)

    // Was previously loaded & needs to be pushed to import map
    if(previous && '{{ $as }}') {
        // Throw error when previously imported under different alias. Otherwise continue
        if(previous.dataset.alias !== '{{ $as }}') {
            throw `BUNDLING ERROR: '{{ $as }}' already imported as '\${previous.dataset.alias}'`
        }
    }

    // Handle CSS injection
    if('{{ $module }}'.endsWith('.css') || '{{ $module }}'.endsWith('.scss')) {
        return import('{{ $module }}').then(result => {
            window.x_inject_styles(result.default, previous)
        })
    }

    // Assign the import to the window.x_import_modules object (or invoke IIFE)
    '{{ $as }}'
        // Assign it under an alias
        ? window.x_import_modules['{{ $as }}'] = import('{{ $module }}')
        // Only import it (for IIFE no alias needed)
        : import('{{ $module }}')
})();
