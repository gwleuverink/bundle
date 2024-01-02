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

    public function __construct(string $expression)
    {
        try {
            [$this->import, $this->module] = $this->arguments($expression);
        } catch(Throwable $e) {
            throw new InvalidArgumentException('The @bundle directive expects exactly two arguments.');
        }
    }

    public function render(): string
    {
        $js = <<< JS
        import $this->import from '$this->module';
        JS;

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
