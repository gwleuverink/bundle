<?php

use Illuminate\Support\Facades\Route;
use Leuverink\Bundle\Bundlers\Bun;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    Bun::construct()->build('', '', '');
});
