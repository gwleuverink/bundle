<?php

namespace Leuverink\Bundle\Commands;

use Throwable;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Finder\SplFileInfo;

use function Laravel\Prompts\info;
use function Laravel\Prompts\error;

class Build extends Command
{
    protected $signature = 'bundle:build';
    protected $description = 'Scan resource directory and bundle all imports for production';

    public function handle(Finder $finder): int
    {
        $errors = 0;

        // Find all usages of x-bundle
        collect(config('bundle.build_paths'))
            // Find all files matching given glob pattern
            ->map(fn($glob) => $finder->files()->in($glob)->depth(0))
            // Map them to an array
            ->flatMap(fn(Finder $iterator) => iterator_to_array($iterator))
            // pregmatch each file for x-bundle components
            ->flatMap(fn(SplFileInfo $file) => preg_grep('/^<x-bundle.*?>$/', file($file)))
            // filter uniques
            ->unique()
            // Then render the blade! The component does the rest
            ->each(function($component) use (&$errors) {
                try {
                    Blade::render($component);
                    $this->components->task($component);
                } catch(Throwable $e) {
                    $this->components->error($component);
                    $errors++;
                }
            });


        if($errors) {
            error('Bundle compiled with errors');
            return static::FAILURE;
        } else {
            info('Bundle compiled successfully!');
            return static::SUCCESS;
        }

    }
}
