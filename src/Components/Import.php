<?php

namespace Leuverink\Bundle\Components;

use Illuminate\View\Component;
use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Exceptions\BundlingFailedException;

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

    protected function bundle()
    {
        $js = $this->core();

        // Render script tag with bundled code
        return view('x-import::script', [
            'bundle' => BundleManager::new()->bundle($js),
        ]);
    }

    protected function core(): string
    {
        return <<< JS
            // First make sure window.x_import_modules exists
            if(!window.x_import_modules) window.x_import_modules = {}

            // Assign the import to the window.x_import_modules object (or invoke IIFE)
            '{$this->as}'
                // Assign it under an alias
                ? window.x_import_modules['{$this->as}'] = import('{$this->module}')
                // Only import it (for IIFE no alias needed)
                : import('{$this->module}')

            // Expose _import function
            window._import = async function(alias, exportName = 'default') {

                // Wait for module to become available (Needed for Alpine support)
                const module = await poll(
                    () => window.x_import_modules[alias],
                    1000,
                    5
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
            async function poll(success, maxDuration, interval) {
                const startTime = new Date().getTime();

                while (true) {
                    // If the success callable returns something truthy, return
                    let result = success()
                    if (result) return result;

                    // Check if maxDuration has elapsed
                    const elapsedTime = new Date().getTime() - startTime;
                    if (elapsedTime >= maxDuration) {
                        console.info(`Unable to resolve '\${alias}'. Operation timed out.`)
                        throw `BUNDLE TIMEOUT: '\${alias}' could not be resolved`;
                    }

                    // Wait for a set interval
                    await new Promise(resolve => setTimeout(resolve, interval));
                }
            }
        JS;
    }
}
