<?php

namespace Leuverink\Bundle\Bundlers;

use Exception;
use Illuminate\Support\Facades\Process;
use Leuverink\Bundle\Contracts\Bundler;
use Symfony\Component\Finder\SplFileInfo;
use Leuverink\Bundle\Traits\Constructable;
use Leuverink\Bundle\Exceptions\BundlingFailedException;

class Bun implements Bundler
{

    use Constructable;

    public function build(string $inputPath, string $outputPath, string $fileName): SplFileInfo
    {
        $result = Process::run(base_path('node_modules/.bin/bun build'))
            ->throw(function ($res): void {
                throw new BundlingFailedException($res);
            })->output();

        dd($result);

        return new SplFileInfo();
    }

}
