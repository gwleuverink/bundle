<?php

use Leuverink\Bundle\BundleManager;

it('can find and build scripts', function() {
    $manager = BundleManager::new();

    // Scan the fixtures dir as build path
    config('bundle.build_paths', [
        __DIR__.'/../fixtures'
    ]);

    // Make sure all cached scripts are cleared
    $this->artisan('bundle:clear');
    $manager->buildDisk()->assertDirectoryEmpty('');

    // Execute build command
    $this->artisan('bundle:build');

    // Assert expected scripts are present
    expect($manager->buildDisk()->allFiles())->toHaveCount(1);
});
