<?php

use Leuverink\Bundle\BundleManager;

it('clears temp disk', function() {
    $manager = BundleManager::new();

    $manager->tempDisk()->put('foo.js', 'bar');
    $manager->tempDisk()->assertExists('foo.js');

    $this->artisan('bundle:clear');
    $manager->tempDisk()->assertMissing('foo.js');
});

it('clears temp disk recursively', function() {
    $manager = BundleManager::new();

    $manager->tempDisk()->put('foo/bar.js', 'baz');
    $manager->tempDisk()->assertExists('foo/bar.js');

    $this->artisan('bundle:clear');
    $manager->tempDisk()->assertMissing('foo/bar.js');
});

it('clears build disk', function() {
    $manager = BundleManager::new();

    $manager->buildDisk()->put('foo.js', 'bar');
    $manager->buildDisk()->assertExists('foo.js');

    $this->artisan('bundle:clear');
    $manager->buildDisk()->assertMissing('foo.js');
});

it('clears build disk recursively', function() {
    $manager = BundleManager::new();

    $manager->buildDisk()->put('foo/bar.js', 'baz');
    $manager->buildDisk()->assertExists('foo/bar.js');

    $this->artisan('bundle:clear');
    $manager->buildDisk()->assertMissing('foo/bar.js');
});
