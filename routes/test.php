<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::any('/getPetsFromMyfoodiepet', function () {
    list($s1, $s2) = explode(' ', microtime());
    $microtime = sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'tenantid' => '00ae459e842642f78b9ab0d8e7c027b4',
        'appid' => '6259662812989361028'
    ])
        ->replaceHeaders([
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36'
        ])
        ->withQueryParameters([
            '_' => $microtime,
            'page' => 1,
            'size' => 999,
            'type' => 1
        ])->get('https://cdp.myfoodiepet.com/deepexi-dm-admin/api/v1/petBreed/page');

    if ($response->ok()) {
        dd($response->json()['payload']['content']);
    } else {
        dd('完蛋');
    }

    dd('完成');

    /**
     * ?
     * _=1729741375303
     * &page=1
     * &size=999
     * &type=2
     */
});

Route::any('/test', function (Request $request) {
    dd(config('filesystems.disks.public.root'));
    return response()->json([
        'router' => 'api',
        'ip' => $request->ip(),
        'client_ip' => $request->getClientIp()
    ]);
});

Route::post('/shop_service', function (Request $request, \App\Services\ShopServiceService $service) {
    dd($service->createServiceInfo($request->post(), false));
});

Route::any('/request', function (Request $request) {
    return response()->json([
        'code' => 200,
        'message' => __('http_response.Ïsuccess'),
        'payload' => [
            'content' => $request->method()
        ]
    ]);
});