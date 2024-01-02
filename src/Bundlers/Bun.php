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
            '--entrypoints' => $inputPath.$fileName,
            '--outdir' => $outputPath,
            '--minify'
        ];

        Process::run("{$path}bun build {$this->args($options)}")
            ->throw(function ($res): void {
                throw new BundlingFailedException($res);
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
                ->append($key == 0 ? '' : $key)->append(' ')
                ->append($option)->append(' ')
                ->replace('  ', ' ')
                ->toString();
        });
    }
}
