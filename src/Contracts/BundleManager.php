<?php

namespace Leuverink\Bundle\Contracts;

use SplFileInfo;
use Illuminate\Http\Response;
use Leuverink\Bundle\Contracts\Bundler;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository as RepositoryContract;


interface BundleManager
{
    public function __construct(Bundler $bundler);

    /** Bundles a given script */
    public function bundle(string $script): SplFileInfo;

    /** Get an instance of the temporary disk the bundler reads from */
    public function tempDisk(): Filesystem;

    /** Get an instance of the temporary disk the bundler writes to */
    public function buildDisk(): Filesystem;

    /** Get the contents of a given bundle */
    public function bundleContents($fileName): Response;

    /** Get the bundle config */
    public function config(): RepositoryContract;

    /** Get the contents of a given chunk */
    // public function chunkContents($fileName): Response;
}
