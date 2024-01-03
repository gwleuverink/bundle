<?php

namespace Leuverink\Bundle\Directives;

use Throwable;
use InvalidArgumentException;
use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Contracts\Directive;
use Leuverink\Bundle\Traits\Constructable;

class BundleDirective implements Directive
{
    use Constructable;

    protected string $import;
    protected string $module;

    public function __construct(string $module)
    {
        $this->module = str($module)
            ->replaceMatches('/[\'"`“”‘’]/u', '') // remove quotes
            ->replace(' ', '') // remove spaces
            ->toString();
    }

    public function render(): string
    {
        $js = <<< JS
        import filter from "$this->module";
        JS;

        // $js = <<< JS
        // (() => {
        //     return import('$this->module')
        // })()
        // JS;

        dd(BundleManager::new()->bundle($js));
    }

    private function arguments(string $expression): array
    {
        return str($expression)
            ->replaceMatches('/[\'"`“”‘’]/u', '') // remove quotes
            ->replace(' ', '') // remove spaces
            ->explode(',')
            ->toArray();
    }
}
