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
        public bool $inline = false
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

    /** Builds the core JavaScript & packages it up in a bundle */
    protected function bundle()
    {
        $js = $this->core();

        // Render script tag with bundled code
        return view('x-import::script', [
            'bundle' => $this->manager()->bundle($js),
        ]);
    }

    /** Get an instance of the BundleManager */
    protected function manager(): BundleManagerContract
    {
        return BundleManager::new();
    }

    /** Determines wherether to raise a console error or throw a PHP exception */
    protected function raiseConsoleErrorOrException(BundlingFailedException $e)
    {
        if (app()->hasDebugModeEnabled()) {
            throw $e;
        }

        report($e);

        return <<< HTML
            <!--[BUNDLE: {$this->as} from '{$this->module}']-->
            <script data-bundle="{$this->module}">console.error('BUNDLING ERROR: import {$this->module} as {$this->as}')</script>
            <!--[ENDBUNDLE]>-->
        HTML;
    }

    /** Buildsb Bundle's core JavaScript */
    protected function core(): string
    {
        $timeout = $this->manager()->config()->get('import_resolution_timeout');

        return <<< JS
            // First make sure window.x_import_modules exists
            if(!window.x_import_modules) window.x_import_modules = {};

            // Invoke IIFE so we can break out of execution when needed
            (() => {

                // Check if module is already loaded under a different alias
                const previous = document.querySelector(`script[data-bundle="{$this->module}"`)

                // Was previously loaded & needs to be pushed to import map
                if(previous && '{$this->as}') {
                    // Throw error improve debugging experience
                    if(previous.dataset.alias !== '{$this->as}') {
                        throw `BUNDLING ERROR: '{$this->as}' already imported as '\${previous.dataset.alias}'`
                    }
                }

                // Assign the import to the window.x_import_modules object (or invoke IIFE)
                '{$this->as}'
                    // Assign it under an alias
                    ? window.x_import_modules['{$this->as}'] = import('{$this->module}')
                    // Only import it (for IIFE no alias needed)
                    : import('{$this->module}')
            })()


            // Expose _import function as soon as possible
            window._import = async function(alias, exportName = 'default') {

                // Wait for module to become available (Needed for Alpine support)
                const module = await poll(
                    () => window.x_import_modules[alias],
                    {$timeout}, 5, alias
                )

                if(module === undefined) {
                    console.info('When invoking _import() from a script tag make sure it has type="module"')
                    throw `BUNDLE ERROR: '\${alias}' not found`;
                }

                return module[exportName] !== undefined
                    // Return export if it exists
                    ? module[exportName]
                    // Otherwise the entire module
                    : module
            }


            // Import polling helper
            async function poll(success, timeout, interval, ref) {
                const startTime = new Date().getTime();

                while (true) {
                    // If the success callable returns something truthy, return
                    let result = success()
                    if (result) return result;

                    // Check if timeout has elapsed
                    const elapsedTime = new Date().getTime() - startTime;
                    if (elapsedTime >= timeout) {
                        throw `BUNDLE TIMEOUT: '\${ref}' could not be resolved`;
                    }

                    // Wait for a set interval
                    await new Promise(resolve => setTimeout(resolve, interval));
                }
            }
        JS;
    }
}
