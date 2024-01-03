<?php

namespace Leuverink\Bundle\Exceptions;

use RuntimeException;
use Spatie\Ignition\Contracts\Solution;
use Spatie\Ignition\Contracts\BaseSolution;
use Illuminate\Contracts\Process\ProcessResult;
use Spatie\Ignition\Contracts\ProvidesSolution;

class BundlingFailedException extends RuntimeException implements ProvidesSolution
{
    public ProcessResult $result;

    public function __construct(ProcessResult $result, $script = null)
    {
        $this->result = $result;

        $failed = $script ?? $result->command();

        parent::__construct(
            "Bundling failed: {$failed}",
            $result->exitCode() ?? 1,
        );
    }

    public function getSolution(): Solution
    {
        // dd($this->result->errorOutput());

        return match (true) {
            str_contains($this->result->errorOutput(), 'bun: No such file or directory') => $this->bunNotInstalledSolution(),
            str_contains($this->result->errorOutput(), 'error: Could not resolve') => $this->moduleNotResolvableSolution(),
            default => BaseSolution::create()
                ->setSolutionTitle('Failed to run the following process:')
                ->setSolutionDescription($this->result->command())
        };
    }

    private function bunNotInstalledSolution()
    {
        return BaseSolution::create()
                ->setSolutionTitle('Bun is not installed.')
                ->setSolutionDescription("Bun is not installed. Try running `npm install bun --save-dev`");
    }

    private function moduleNotResolvableSolution()
    {
        $module = str($this->result->errorOutput())->after('"')->before('"')->toString();

        return BaseSolution::create()
                ->setSolutionTitle("Unable to resolve module '{$module}'")
                ->setSolutionDescription("{$module}. Try running `npm install {$module}` or check the path to the import if it's a script in your resources directory.");
    }

}
