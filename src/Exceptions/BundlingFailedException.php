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

    public function __construct(ProcessResult $result)
    {
        $this->result = $result;

        parent::__construct(
            "The transpilation process failed: `{$result->errorOutput()}`",
            $result->exitCode() ?? 1,
        );
    }

    public function getSolution(): Solution
    {
        // if (str_contains($this->result->errorOutput(), '[ERROR] Could not resolve')) {
        //     return BaseSolution::create('Failed to resolve a module')
        //         ->setSolutionDescription('A module is not installed or not resolvable via the configured paths. Please refer to the x-script config');
        // }

        return match (true) {
            str_contains($this->result->errorOutput(), 'bun: No such file or directory') => $this->bunNotInstalledSolution(),
            default => BaseSolution::create('Failed to run the following process:')->setSolutionDescription($this->result->command())
        };
    }

    private function bunNotInstalledSolution()
    {
        return BaseSolution::create('Bun is not installed.')
                ->setSolutionDescription("Bun is not installed. Try running `npm install bun --save-dev`");
    }

}
