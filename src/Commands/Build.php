<?php

namespace Leuverink\Bundle\Commands;

use Throwable;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Finder\SplFileInfo;

use function Laravel\Prompts\info;
use function Laravel\Prompts\error;
use function Laravel\Prompts\progress;

class Build extends Command
{
    protected $signature = 'bundle:build';
    protected $description = 'Scan build_patsh and bundle all imports for production';

    public function handle(Finder $finder): int
    {
        $this->call('bundle:clear');

        $errors = 0;

        // Find and bundle all components
        collect(config('bundle.build_paths'))
            // Find all files matching given glob pattern
            ->map(fn($glob) => $finder->files()->in($glob)->depth(0))
            // Map them to an array
            ->flatMap(fn(Finder $iterator) => iterator_to_array($iterator))
            // Pregmatch each file for x-bundle components
            ->flatMap(fn(SplFileInfo $file) => preg_grep('/^<x-bundle.*?>$/', file($file)))
            // Filter uniques
            ->unique()
            // Start progress bar
            ->pipe(fn($components) => progress('Building Bundle imports', $components, function($component) use (&$file, $errors) {
                try {
                    // Render the blade. The component does the rest
                    $this->components->task(
                        "$component from: $file",
                        fn() => Blade::render($component)
                    );
                } catch(Throwable $e) {
                    $errors++;
                }
            }));

        if($errors) {
            error('Bundle compiled with errors');
            return static::FAILURE;
        } else {
            info('Bundle compiled successfully!');
            return static::SUCCESS;
        }

    }
}
