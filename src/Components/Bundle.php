<?php

namespace Leuverink\Bundle\Components;

use Illuminate\View\Component;
use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Exceptions\BundlingFailedException;

class Bundle extends Component
{
    public function __construct(
        public string $import,
        public string $as,
        public bool $inline = false // TODO: Implement this
    ) {
    }

    public function render()
    {
        // First make sure window._bundle_modules exists
        // and assign the import to that object.
        // ---------------------------------------------
        // Then we expose a _bundle function that
        // can retreive the module as a Promise
        $js = <<< JS
            if(!window._bundle_modules) window._bundle_modules = {}
            window._bundle_modules.{$this->as} = import('{$this->import}')

            window._bundle = async function(alias, exportName = 'default') {
                let module = await window._bundle_modules[alias]
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
            <!--[BUNDLE: {$this->as} from '{$this->import}']-->
            <script data-bundle="{$this->as}">console.error('BUNDLING ERROR: import {$this->import} as {$this->as}')</script>
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
