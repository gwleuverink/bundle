<?php

use Leuverink\Bundle\BundleManager;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $js = <<< JS
        let foo = 'hello world!'
        alert(foo)
    JS;

    $bundle = BundleManager::new()->bundle($js);

    dd($bundle);
});
