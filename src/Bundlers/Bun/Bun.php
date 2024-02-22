<?php

namespace Leuverink\Bundle\Bundlers\Bun;

use SplFileInfo;
use Illuminate\Support\Facades\Process;
use Leuverink\Bundle\Contracts\Bundler;
use Leuverink\Bundle\Traits\Constructable;
use Leuverink\Bundle\Exceptions\BundlingFailedException;

class Bun implements Bundler
{
    use Constructable;

    public function build(
        string $inputPath,
        string $outputPath,
        string $fileName,
        bool $sourcemaps = false,
        bool $minify = true
    ): SplFileInfo {

        $bun = base_path('node_modules/.bin/bun');
        $buildScript = __DIR__ . '/bin/bun.js';
        $options = [
            '--entrypoint' => $inputPath . $fileName,
            '--inputPath' => $inputPath,
            '--outputPath' => $outputPath,
            ...[$sourcemaps ? '--sourcemaps' : ''],
            ...[$minify ? '--minify' : ''],
        ];

        Process::run("{$bun} {$buildScript} {$this->args($options)}")
            ->throw(fn ($res) => throw new BundlingFailedException($res));

        return new SplFileInfo($outputPath . $fileName);
    }

    //--------------------------------------------------------------------------
    // Helper methods
    //--------------------------------------------------------------------------
    private function args(array $options): string
    {
        return collect($options)->reduce(function ($carry, $option, $key) {
            return str($carry)
                ->append(is_int($key) ? '' : $key)->append(' ')
                ->append($option)->append(' ')
                ->replace('  ', ' ')
                ->toString();
        });
    }
}
