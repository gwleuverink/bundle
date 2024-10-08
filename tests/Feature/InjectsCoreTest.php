<?php

use Illuminate\Support\Facades\Route;

it('injects core into head tag', function () {
    Route::get('test-inject-in-response', fn () => '<html><head></head></html>');

    $this->get('test-inject-in-response')
        ->assertOk()
        ->assertSee('data-bundle="core"', false)
        ->assertSee('<!--[BUNDLE-CORE]-->', false);
});

it('injects core into html body when no head tag is present', function () {
    Route::get('test-inject-in-response', fn () => '<html></html>');

    $this->get('test-inject-in-response')
        ->assertOk()
        ->assertSee('data-bundle="core"', false)
        ->assertSee('<!--[BUNDLE-CORE]-->', false);
});

it('doesnt inject core into responses without a closing html tag', function () {
    Route::get('test-inject-in-response', fn () => 'OK');

    $this->get('test-inject-in-response')
        ->assertOk()
        ->assertDontSee('data-bundle="core"', false)
        ->assertDontSee('<!--[BUNDLE-CORE]-->', false);
});
