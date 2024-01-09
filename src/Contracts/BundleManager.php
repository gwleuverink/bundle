<?php

namespace Leuverink\Bundle\Contracts;

use SplFileInfo;
use Leuverink\Bundle\Contracts\Bundler;
use Illuminate\Contracts\Filesystem\Filesystem;

interface BundleManager
{
    public function __construct(Bundler $bundler);

    /** Bundles a given script */
    public function bundle(string $script): SplFileInfo;

    /** Get an instance of the temporary disk the bundler reads from */
    public function tempDisk(): Filesystem;

    /** Get an instance of the temporary disk the bundler writes to */
    public function buildDisk(): Filesystem;
}
