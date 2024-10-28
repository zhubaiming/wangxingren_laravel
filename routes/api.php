<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::any('/', function (Request $request) {
    dd('a');
});

Route::any('/test', function (Request $request) {
    dd($request);
    return response()->json([
        'router' => 'api',
        'ip' => $request->ip(),
        'client_ip' => $request->getClientIp()
    ]);
});

Route::post('/shop_service', function (Request $request, \App\Services\ShopServiceService $service) {
    dd($service->createServiceInfo($request->post(), false));
});