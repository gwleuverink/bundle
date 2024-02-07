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
    expect($manager->buildDisk()->allFiles())->toBeGreaterThanOrEqual(2); // core + import
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
    expect($manager->buildDisk()->allFiles())->toBeGreaterThanOrEqual(3); // core + 2 imports
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
    expect($manager->buildDisk()->allFiles())->toHaveCount(2); // core + markdown file
});

it('includes Bundle core', function () {
    $manager = BundleManager::new();

    // Scan empty dir
    config()->set('bundle.build_paths', [
        realpath(getcwd() . '/tests/Fixtures/resources/empty'),
    ]);

    // Make sure all cached scripts are cleared
    $this->artisan('bundle:clear');
    $manager->buildDisk()->assertDirectoryEmpty('');

    // Execute build command
    $this->artisan('bundle:build');

    // Expect it to at least have 1 bundle. This is the core,
    // since the scan path contains no other usages of x-import.
    expect($manager->buildDisk()->allFiles())->toHaveCount(1);

    // For good measure, make sure it contains the expected code. (kinda flaky)
    $file = $manager->buildDisk()->path(
        head($manager->buildDisk()->files())
    );

    expect(file_get_contents($file))
        ->toContain('window.x_import_modules={}')
        ->toContain('window._import=async function');
});
