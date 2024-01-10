<?php

use Leuverink\Bundle\BundleManager;

it('imports from relative path alias', function() {
    $manager = BundleManager::new();

    // Scan the fixtures dir as build path
    config()->set('bundle.build_paths', [
        realpath(getcwd().'/tests/Fixtures')
    ]);

    // Make sure all cached scripts are cleared
    $this->artisan('bundle:clear');
    $manager->buildDisk()->assertDirectoryEmpty('');

    // Execute build command
    $this->artisan('bundle:build');

    // Assert expected scripts are present
    expect($manager->buildDisk()->allFiles())->toHaveCount(1);
});
