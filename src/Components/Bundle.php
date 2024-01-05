<?php

namespace Leuverink\Bundle\Components;

use Illuminate\View\Component;
use Leuverink\Bundle\BundleManager;

class Bundle extends Component
{
    public function __construct(
        public string $import,
        public string $as
    ){ }

    public function render()
    {
        $js = <<< JS
            if(!window._bundle_modules) window._bundle_modules = {}

            window._bundle = async function(alias, exportName = 'default') {
                let module = await window._bundle_modules[alias]
                return module[exportName]
            }

            window._bundle_modules.$this->as = import('$this->import')
        JS;

        $bundle = file_get_contents(
            BundleManager::new()->bundle($js)
        );

        return view('bundle::bundle', [
            'bundle' => $bundle
        ]);
    }
}
