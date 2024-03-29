<?php

use Leuverink\Bundle\BundleManager;
use Leuverink\Bundle\Tests\TestCase;
use Leuverink\Bundle\Tests\DuskTestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)
    ->group('unit')
    ->in('Unit');

uses(TestCase::class)
    ->group('feature')
    ->in('Feature');

uses(DuskTestCase::class)
    ->group('browser')
    ->in('Browser');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
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
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function bundle(string $code = '')
{
    $file = BundleManager::new()->bundle($code);

    $output = file_get_contents($file->getPathname());

    return expect($output);
}
