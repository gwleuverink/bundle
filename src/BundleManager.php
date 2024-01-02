<?php

namespace Leuverink\Bundle;

use SplFileInfo;
use Leuverink\Bundle\Contracts\Bundler as BundlerContract;
use Leuverink\Bundle\Contracts\BundleManager as BundleManagerContract;
use Leuverink\Bundle\Traits\Constructable;

class BundleManager implements BundleManagerContract
{
    use Constructable;

    protected readonly BundlerContract $bundler;

    public function __construct(BundlerContract $bundler)
    {
        $this->bundler = $bundler;
    }

    public function bundle(string $script): SplFileInfo
    {
        $this->bundler->build(
            inputPath: '',
            outputPath: '',
            fileName: ''
        );
        return new SplFileInfo('');
    }
}
