<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'playground');

Route::prefix('test')->group(function () {
    Route::view('local-import', 'test.local-import');
    Route::view('node-module-named-import', 'test.node-module-named-import');
    Route::view('node-module-per-method-import', 'test.node-module-per-method-import');
    Route::view('alpine-component-init-function', 'test.alpine-component-init-function');
    Route::view('alpine-component-init-directive', 'test.alpine-component-init-directive');
});
