<?php

namespace Leuverink\Bundle\Components;

use Illuminate\View\Component;
use Leuverink\Bundle\BundleManager;

class Bundle extends Component
{
    public function __construct(
        public string $import,
        public string $as,
        public bool $inline = false // TODO: Implement this
    ){ }

    public function render()
    {
        // First make sure window._bundle_modules exists
        // and assign the import to that object.
        // ---------------------------------------------
        // Then we expose a _bundle function that
        // can retreive the module as a Promis
        $js = <<< JS
            if(!window._bundle_modules) window._bundle_modules = {}
            window._bundle_modules.$this->as = import('$this->import')

            window._bundle = async function(alias, exportName = 'default') {
                let module = await window._bundle_modules[alias]
                return module[exportName]
            }
        JS;

        // Bundle it up
        $bundle = BundleManager::new()->bundle($js);

        // Render script tag with bundled code
        return view('bundle::bundle', [
            'bundle' => $bundle
        ]);
    }
}
