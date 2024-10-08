<?php

use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Tests\TestCase;
use Leuverink\Bundle\Tests\DuskTestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->group('unit')
    ->in('Unit');

pest()->extend(TestCase::class)
    ->group('feature')
    ->in('Feature');

pest()->extend(DuskTestCase::class)
    ->group('browser')
    ->in('Browser');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('transpilesTo', function (string $expected = '') {
    // Add newline to passed expectation, this isn't present when passing HEREDOC
    $expected = $expected . PHP_EOL;

    return expect($this->value)->content()->toBe($expected);
});

expect()->extend('content', function (string $expected = '') {
    return expect($this->value);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function bundle(string $code = '')
{
    $file = BundleManager::new()->bundle($code);

    $output = file_get_contents($file->getPathname());

    return expect($output);
}
