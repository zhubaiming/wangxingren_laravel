<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1;

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


Route::prefix('goods')->group(function () {
    Route::apiResource('/category', V1\GoodCategoryController::class);
});

Route::prefix('v1')->group(function () {
    Route::apiResource('/goods_category', V1\GoodCategoryController::class);
    Route::apiResource('/goods', V1\GoodsController::class);
});