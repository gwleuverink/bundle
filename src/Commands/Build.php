<?php

namespace Leuverink\Bundle\Commands;

use Throwable;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Finder\SplFileInfo;

use function Laravel\Prompts\info;
use function Laravel\Prompts\error;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\progress;

class Build extends Command
{
    protected $errors = 0;
    protected $signature = 'bundle:build';
    protected $description = 'Scan build_paths and bundle all imports for production';

    public function handle(Finder $finder): int
    {
        $this->callSilent('bundle:clear');

        // Find and bundle all components
        collect(config('bundle.build_paths'))
            // Find all files matching *.blade.*
            ->map(fn ($path) => $finder->in($path)->files()->name('*.blade.*'))
            // Map them to an array
            ->flatMap(fn (Finder $iterator) => iterator_to_array($iterator))
            // Pregmatch each file for x-bundle components
            ->flatMap(fn (SplFileInfo $file) => preg_grep('/<x-bundle.*?>$/', file($file)))
            // Trim whitespace
            ->map(fn ($component) => trim($component))
            // Filter uniques
            ->unique()
            // Handle no no imports found
            ->whenEmpty(fn () => warning('No usages of <x-bundle /> found in your build_paths.'))
            // Start progress bar & render components
            ->whenNotEmpty(function ($components) {
                progress(
                    'Building Bundle imports',
                    $components,
                    fn ($component) => $this->renderComponent($component)
                );
            });

        if ($this->errors) {
            error('Bundle compiled with errors');

            return static::FAILURE;
        } else {
            info('Bundle compiled successfully!');

            return static::SUCCESS;
        }
    }

    protected function renderComponent(string $component)
    {
        try {
            // Render the component. The Blade compiler invokes the bundler
            $this->components->task(
                $component,
                fn () => Blade::render($component)
            );
        } catch (Throwable $e) {
            $this->errors++;
        }
    }
}
