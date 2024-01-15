<?php

use Leuverink\Bundle\BundleManager;

it('generates a bundle', function () {
    $manager = BundleManager::new();

    // Scan the fixtures dir as build path
    config()->set('bundle.build_paths', [
        realpath(getcwd() . '/tests/Fixtures/resources'),
    ]);

    // Make sure all cached scripts are cleared
    $this->artisan('bundle:clear');
    $manager->buildDisk()->assertDirectoryEmpty('');

    // Execute build command
    $this->artisan('bundle:build');

    // Assert expected scripts are present
    expect($manager->buildDisk()->allFiles())->toBeGreaterThanOrEqual(1);
});

it('scans paths recursively', function () {
    $manager = BundleManager::new();

    // Scan the fixtures dir as build path
    config()->set('bundle.build_paths', [
        realpath(getcwd() . '/tests/Fixtures/resources'),
    ]);

    // Make sure all cached scripts are cleared
    $this->artisan('bundle:clear');
    $manager->buildDisk()->assertDirectoryEmpty('');

    // Execute build command
    $this->artisan('bundle:build');

    // Assert expected scripts are present
    expect($manager->buildDisk()->allFiles())->toBeGreaterThanOrEqual(2);
});

it('scans wildcard blade extentions like both php & md', function () {
    $manager = BundleManager::new();

    // Scan the fixtures dir as build path
    config()->set('bundle.build_paths', [
        realpath(getcwd() . '/tests/Fixtures/resources/markdown'),
    ]);

    // Make sure all cached scripts are cleared
    $this->artisan('bundle:clear');
    $manager->buildDisk()->assertDirectoryEmpty('');

    // Execute build command
    $this->artisan('bundle:build');
    expect($manager->buildDisk()->allFiles())->toHaveCount(1);
});
