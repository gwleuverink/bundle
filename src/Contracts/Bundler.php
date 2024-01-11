<?php

namespace Leuverink\Bundle\Contracts;

use SplFileInfo;

interface Bundler
{
    public function build(string $inputPath, string $outputPath, string $fileName, bool $sourcemaps = false): SplFileInfo;
}
