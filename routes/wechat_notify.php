<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/payment/jsapi', function (Request $request, \App\Services\Wechat\MiniProgramPaymentService $service) {
//    dd($request->headers);
    $service->decryptNotify($request);
//    dd($service->decryptNotify($request));
});