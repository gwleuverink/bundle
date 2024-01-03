<?php

namespace Leuverink\Bundle\Bundlers;

use Exception;
use SplFileInfo;
use Illuminate\Support\Facades\Process;
use Leuverink\Bundle\Contracts\Bundler;
use Leuverink\Bundle\Traits\Constructable;
use Leuverink\Bundle\Exceptions\BundlingFailedException;

class Bun implements Bundler
{

    use Constructable;

    public function build(string $inputPath, string $outputPath, string $fileName): SplFileInfo
    {
        $path = base_path('node_modules/.bin/');
        $options = [
            // '--chunk-naming' => 'chunks/[name]-[hash]',
            // '--tsconfig-override' => base_path('jsconfig.json'),
            '--entrypoints' => $inputPath.$fileName,
            '--outdir' => $outputPath,
            '--format' => 'esm',
            // '--root' => '.',
            '--splitting',
            '--minify'
        ];

        Process::run("{$path}bun build {$this->args($options)}")
            ->throw(function ($res) use ($inputPath, $fileName): void {
                $failed = file_get_contents($inputPath.$fileName);
                throw new BundlingFailedException($res, $failed);
            });

        return new SplFileInfo($outputPath.$fileName);
    }

    //--------------------------------------------------------------------------
    // Helper methods
    //--------------------------------------------------------------------------
    private function args(array $options): string
    {
        return collect($options)->reduce(function($carry, $option, $key) {
            return str($carry)
                ->append(is_int($key) ? '' : $key)->append(' ')
                ->append($option)->append(' ')
                ->replace('  ', ' ')
                ->toString();
        });
    }
}
