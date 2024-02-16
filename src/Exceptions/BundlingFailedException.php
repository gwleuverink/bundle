<?php

// @codeCoverageIgnoreStart

namespace Leuverink\Bundle\Exceptions;

use RuntimeException;
use Illuminate\Support\Arr;
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

        // dd($this->output());

        parent::__construct(
            "Bundling failed: {$failed}",
            $result->exitCode() ?? 1,
        );

        // TODO: Consider different approach for providing contextual debug info
        if (app()->isLocal() && config()->get('app.debug')) {
            dump(['error output', $result->output()]);
        }
    }

    /** Format output as defined in error function in bin/utils/dump.js */
    public function output(): object
    {
        $output = json_decode($this->result->errorOutput());
        if (
            gettype($output) === 'object' &&
            property_exists($output, 'id') &&
            property_exists($output, 'message') &&
            property_exists($output, 'output')
        ) {
            return $output;
        }

        return (object) [
            'id' => null,
            'message' => '',
            'output' => $this->result->errorOutput(),
        ];
    }

    public function consoleOutput(): string
    {
        $output = $this->output();

        if ($output->message) {
            return $output->message;
        }

        return Arr::wrap($output->output)[0];
    }

    public function getSolution(): Solution
    {
        return match (true) {
            str_contains($this->result->errorOutput(), 'bun: No such file or directory') => $this->bunNotInstalledSolution(),
            str_contains($this->output()->id, 'bundle:sass-not-installed') => $this->sassNotInstalledSolution(),
            str_contains($this->output()->id, 'bundle:lightningcss-not-installed') => $this->lightningcssNotInstalledSolution(),
            str_contains($this->result->errorOutput(), 'error: Could not resolve') => $this->moduleNotResolvableSolution(),
            str_contains($this->result->errorOutput(), 'tsconfig.json: ENOENT') => $this->missingJsconfigFileSolution(),
            str_contains($this->result->errorOutput(), 'Cannot find tsconfig') => $this->missingJsconfigFileSolution(),
            default => BaseSolution::create()
                ->setSolutionTitle('Failed to run the following process:')
                ->setSolutionDescription($this->result->command())
        };
    }

    private function bunNotInstalledSolution()
    {
        return BaseSolution::create()
            ->setSolutionTitle('Bun is not installed.')
            ->setSolutionDescription('Bun is not installed. Try running `npm install bun --save-dev`');
    }

    private function sassNotInstalledSolution()
    {
        return BaseSolution::create()
            ->setSolutionTitle('Sass is not installed.')
            ->setSolutionDescription('You need to install Sass in order to load .scss files. Try running `npm install sass --save-dev`');
    }

    private function lightningcssNotInstalledSolution()
    {
        return BaseSolution::create()
            ->setSolutionTitle('Lightning CSS is not installed.')
            ->setSolutionDescription('You need to install Lightning CSS in order to load .css files. Try running `npm install lightningcss --save-dev`');
    }

    private function moduleNotResolvableSolution()
    {
        $module = str($this->result->errorOutput())->after('"')->before('"')->toString();

        return BaseSolution::create()
            ->setSolutionTitle("Unable to resolve module '{$module}'")
            ->setSolutionDescription("{$module}. Try running `npm install {$module}` or check or make sure you have a `jsconfig.json` in case you are importing a local module.");
    }

    private function missingJsconfigFileSolution()
    {
        return BaseSolution::create()
            ->setSolutionTitle('jsconfig.json file missing')
            ->setSolutionDescription('A jsconfig file is required in order to define your bundle\'s path mapping. Please create a jsconfig in your project root.');
    }
}
// @codeCoverageIgnoreEnd
