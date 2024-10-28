<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/shop_service', function (Request $request) {
    dd('web/shop_service');
});