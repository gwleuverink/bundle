<?php

namespace Leuverink\Bundle\Components;

use Illuminate\View\Component;
use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Exceptions\BundlingFailedException;
use Leuverink\Bundle\Contracts\BundleManager as BundleManagerContract;

class Import extends Component
{
    public function __construct(
        public string $module,
        public ?string $as = null,
        public bool $inline = false,
        public bool $init = false
    ) {
    }

    public function render()
    {
        try {
            return $this->bundle();
        } catch (BundlingFailedException $e) {
            return $this->raiseConsoleErrorOrException($e);
        }
    }

    /** Builds the imported JavaScript & packages it up in a bundle */
    protected function bundle()
    {
        $js = $this->import();

        // Render script tag with bundled code
        return view('x-import::script', [
            'bundle' => $this->manager()->bundle($js, [
                'init' => $this->init,
            ]),
        ]);
    }

    /** Get an instance of the BundleManager */
    protected function manager(): BundleManagerContract
    {
        return BundleManager::new();
    }

    /** Determines wherether to raise a console error or throw a PHP exception when the BundleManager throws an Exception */
    protected function raiseConsoleErrorOrException(BundlingFailedException $e)
    {
        if (app()->hasDebugModeEnabled()) {
            throw $e;
        }

        report($e);

        return <<< HTML
            <!--[BUNDLE: {$this->as} from '{$this->module}']-->
            <script data-module="{$this->module}" data-alias="{$this->as}">throw "BUNDLING ERROR: {$e->consoleOutput()}"</script>
            <!--[ENDBUNDLE]>-->
        HTML;
    }

    /** Builds a bundle for the JavaScript import */
    protected function import(): string
    {
        return <<< JS
            //--------------------------------------------------------------------------
            // Expose x_import_modules map
            //--------------------------------------------------------------------------
            if(!window.x_import_modules) window.x_import_modules = {};

            //--------------------------------------------------------------------------
            // Import the module & push to x_import_modules
            // Invoke IIFE so we can break out of execution when needed
            //--------------------------------------------------------------------------
            (() => {

                // Check if module is already loaded under a different alias
                const previous = document.querySelector(`script[data-module="{$this->module}"]`)

                // Was previously loaded & needs to be pushed to import map
                if(previous && '{$this->as}') {
                    // Throw error when previously imported under different alias. Otherwise continue
                    if(previous.dataset.alias !== '{$this->as}') {
                        throw `BUNDLING ERROR: '{$this->as}' already imported as '\${previous.dataset.alias}'`
                    }
                }

                // Import was marked as invokable
                if('{$this->init}') {

                    return import('{$this->module}')
                        .then(invokable => {
                            if(typeof invokable.default !== 'function') {
                                throw `BUNDLING ERROR: '{$this->module}' not invokable - default export is not a function`
                            }

                            try {
                                invokable.default()
                            } catch(e) {
                                throw `BUNDLING ERROR: unable to invoke '{$this->module}' - '\${e}'`
                            }
                        })
                }

                // Handle CSS injection
                if('{$this->module}'.endsWith('.css') || '{$this->module}'.endsWith('.scss')) {
                    return import('{$this->module}').then(result => {
                        window.x_inject_styles(result.default, previous)
                    })
                }

                // Assign the import to the window.x_import_modules object (or invoke IIFE)
                '{$this->as}'
                    // Assign it under an alias
                    ? window.x_import_modules['{$this->as}'] = import('{$this->module}')
                    // Only import it (for IIFE no alias needed)
                    : import('{$this->module}')
            })();

        JS;
    }
}
