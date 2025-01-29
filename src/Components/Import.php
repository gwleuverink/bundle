<?php

namespace Leuverink\Bundle\Components;

use Illuminate\View\Component;
use Illuminate\Support\Stringable;
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
    ) {}

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
        // Wraps import with execution logic
        $js = (string) view('x-import::import', [
            'module' => $this->module,
            'init' => $this->init,
            'as' => $this->as,
        ]);

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

        $output = str()
            ->of($e->consoleOutput())
            ->whenContains('error:', fn (Stringable $string) => $string->after('error:'))
            ->trim();

        return <<< HTML
            <!--[BUNDLE: {$this->as} from '{$this->module}']-->
            <script data-module="{$this->module}" data-alias="{$this->as}">throw "BUNDLING ERROR: {$output}"</script>
            <!--[ENDBUNDLE]>-->
        HTML;
    }
}
