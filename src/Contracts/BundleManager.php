<?php

namespace Leuverink\Bundle\Contracts;

use SplFileInfo;
use Mockery\MockInterface;
use Illuminate\Http\Response;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository as RepositoryContract;

interface BundleManager
{
    public function __construct(Bundler $bundler);

    /** Bundles a given script, accepts a optional config argument that overrides the package config */
    public function bundle(string $script, array $config = []): SplFileInfo;

    /** Get the bundle config */
    public function config(): RepositoryContract;

    /** Merge the given config with the default config */
    public function mergeConfig(array $config = []): RepositoryContract;

    /** Get an instance of the temporary disk the bundler reads from */
    public function tempDisk(): Filesystem;

    /** Get an instance of the temporary disk the bundler writes to */
    public function buildDisk(): Filesystem;

    /** Get the contents of a given bundle */
    public function bundleContents($fileName): Response;

    /** Get the contents of a given chunk */
    // public function chunkContents($fileName): Response;

    /** Hashes a given string */
    public function hash($input, $length = 12): string;

    /** Mock for testing */
    public static function fake(): MockInterface;
}
