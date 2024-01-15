<?php

namespace Leuverink\Bundle\Components;

use Illuminate\View\Component;
use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Exceptions\BundlingFailedException;

class Import extends Component
{
    public function __construct(
        public string $module,
        public string $as,
        public bool $inline = false // TODO: Implement this
    ) {
    }

    public function render()
    {
        // First make sure window.x_import_modules exists
        // and assign the import to that object.
        // ---------------------------------------------
        // Then we expose a _import function that
        // can retreive the module as a Promise
        $js = <<< JS
            if(!window.x_import_modules) window.x_import_modules = {}
            window.x_import_modules.{$this->as} = import('{$this->module}')

            window._import = async function(alias, exportName = 'default') {
                let module = await window.x_import_modules[alias]
                return module[exportName]
            }
        JS;

        // Bundle it up
        try {
            return $this->bundle($js);
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
            <script data-bundle="{$this->as}">console.error('BUNDLING ERROR: import {$this->module} as {$this->as}')</script>
            <!--[ENDBUNDLE]>-->
        HTML;
    }

    protected function bundle(string $js)
    {
        $bundle = BundleManager::new()->bundle($js);

        // Render script tag with bundled code
        return view('bundle::bundle', [
            'bundle' => $bundle,
        ]);
    }
}
