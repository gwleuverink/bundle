<?php

namespace Leuverink\Bundle;

use Throwable;
use SplFileInfo;
use Illuminate\Support\Facades\Storage;
use Leuverink\Bundle\Traits\Constructable;
use Leuverink\Bundle\Contracts\Bundler as BundlerContract;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Leuverink\Bundle\Contracts\BundleManager as BundleManagerContract;


class BundleManager implements BundleManagerContract
{
    use Constructable;

    public const BUILD_DIR = 'bundle';
    public const TEMP_DIR = 'bundle-tmp';

    protected readonly BundlerContract $bundler;

    public function __construct(BundlerContract $bundler)
    {
        $this->bundler = $bundler;
    }

    public function bundle(string $script): SplFileInfo
    {
        $fileName = "{$this->hash($script)}.min.js";

        // Return cached file if available
        if (config('bundle.caching_enabled') && $cached = $this->fromDisk($fileName)) {
            return $cached;
        }

        // Create temporary input file
        $this->tempDisk()->put($fileName, $script);

        // Attempt bundling & cleanup
        try {
            $processed = $this->bundler->build(
                inputPath: $this->tempDisk()->path(''),
                outputPath: $this->buildDisk()->path(''),
                fileName: $fileName
            );
        } catch (Throwable $e) {
            $this->tempDisk()->delete($fileName);
            throw $e; // TODO: Consider raising a browser console error instead
        } finally {
            $this->tempDisk()->delete($fileName);
        }

        return $processed;
    }


    //--------------------------------------------------------------------------
    // Helper methods
    //--------------------------------------------------------------------------
    private function tempDisk(): FilesystemContract
    {
        return Storage::build([
            'driver' => 'local',
            'root' => storage_path('app/' . static::TEMP_DIR),
        ]);
    }

    private function buildDisk(): FilesystemContract
    {
        return Storage::build([
            'driver' => 'local',
            'root' => storage_path('app/' . static::BUILD_DIR),
        ]);
    }

    private function hash($input, $length = 12) {
        // Create a SHA-256 hash of the input
        $hash = hash('sha256', $input);

        // Truncate the hash to the specified length
        return substr($hash, 0, $length);
    }

    private function fromDisk(string $fileName): ?SplFileInfo
    {
        if ( ! $this->buildDisk()->exists($fileName)) {
            return null;
        }

        return new SplFileInfo(
            $this->buildDisk()->path($fileName)
        );
    }
}
