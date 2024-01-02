<?php

namespace Leuverink\Bundle\Contracts;

use SplFileInfo;
use Leuverink\Bundle\Contracts\Bundler;

interface BundleManager
{
    public function __construct(Bundler $bundler);

    /** Bundles a given script */
    public function bundle(string $script): SplFileInfo;
}
