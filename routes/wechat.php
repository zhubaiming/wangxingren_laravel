<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Wechat;

Route::withoutMiddleware(['auth.wechat'])->group(function () {
    Route::post('/silentLogin', [Wechat\UserController::class, 'silentLogin']);

    Route::any('/test', function (Request $request, \App\Services\Wechat\MiniAppServerSideService $service) {
//    return response()->json([
//        'ip' => $request->ip(),
//        'client_ip' => $request->getClientIp(),
//        'user'=>\Illuminate\Support\Facades\Auth::guard('wechat')->user(),
//    ]);
//    return $service->code2session('aaaaa');
//        dd(\App\Enums\GenderEnum::man, \App\Enums\GenderEnum::from(1));

        dd([$app_type, $app_id, $openid] = explode('.', \Illuminate\Support\Facades\Crypt::decryptString('eyJpdiI6ImxKeWhkeEtrMGRrcE9UT05JdkliOXc9PSIsInZhbHVlIjoibngzTmlET0F3cmlxOUZuYy9VajlEcEs4S0RBM2FJcUZZMGlaYU0vVzZybWZQSmVPQ0hJVGtydTBlT0EvWnUrdU9VeHVWSURpTXZlL09xMGVTMkcyZmJtL0ZiUWlSTk1XYWRqZE9IcUpUelU9IiwibWFjIjoiNDViN2Q2ZTMzMTg1MDY0NjFhMTc0YjZmMzA3MjNmOTA2ZjBkN2M0MmMwOWZiZjRlNDQ5ZTQxMTM4NWI1M2I5MyIsInRhZyI6IiJ9')));

        dd($app_type, $app_id, $openid);
    });

    Route::get('/app_banners', function () {
        return response()->json([
            'code' => 200,
            'message' => __('http_response.success'),
            'payload' => [
                [
                    'tenantId' => '00ae459e842642f78b9ab0d8e7c027b4',
                    'appId' => 6259662812989361028,
                    'id' => 83,
                    'place' => 10,
                    'picUrl' => 'https://dp-live.oss-cn-beijing.aliyuncs.com/marketing/image/hlDebwlcgq.jpg',
                    'sort' => 1,
                    'soure' => null,
                    'jumpType' => null,
                    'jumpAddress' => null,
                    'remark' => null,
                    'groupId' => 261,
                    'hasVideo' => true,
                    'videoSrc' => 'http://wxsnsdy.tc.qq.com/105/20210/snsdyvideodownload?filekey=30280201010421301f0201690402534804102ca905ce620b1241b726bc41dcff44e00204012882540400&bizid=1023&hy=SH&fileparam=302c020101042530230204136ffd93020457e3c4ff02024ef202031e8d7f02030f42400204045a320a0201000400̰'
                ],
                [
                    'tenantId' => '00ae459e842642f78b9ab0d8e7c027b4',
                    'appId' => 6259662812989361028,
                    'id' => 84,
                    'place' => 10,
                    'picUrl' => 'https://dp-live.oss-cn-beijing.aliyuncs.com/marketing/image/iDjKUTByaW.png',
                    'sort' => 1,
                    'soure' => 2,
                    'jumpType' => 8,
                    'jumpAddress' => '7IARD8A04RK58CQ1NZFFZ',
                    'remark' => null,
                    'groupId' => 263,
                    'hasVideo' => false,
                    'videoSrc' => null
                ],
                [
                    'tenantId' => '00ae459e842642f78b9ab0d8e7c027b4',
                    'appId' => 6259662812989361028,
                    'id' => 87,
                    'place' => 10,
                    'picUrl' => 'https://dp-live.oss-cn-beijing.aliyuncs.com/marketing/image/iKQig9Zfk0.jpg',
                    'sort' => 1,
                    'soure' => 2,
                    'jumpType' => 5,
                    'jumpAddress' => 'https://mp.weixin.qq.com/s/bs4Qzeb2JAbAXv5aVtkiqQ',
                    'remark' => null,
                    'groupId' => 263,
                    'hasVideo' => false,
                    'videoSrc' => null
                ]
            ]
        ]);
    });

    Route::prefix('category')->group(function () {
        Route::get('/pets/{id}', [Wechat\PetController::class, 'category']);
        Route::get('/goods', [Wechat\GoodController::class, 'category']);
    });
});

Route::post('/registerLogin', [Wechat\UserController::class, 'registerLogin']);

Route::apiResource('/pets', Wechat\PetController::class);
Route::resource('/goods', Wechat\GoodController::class)->only(['index', 'show']);

Route::prefix('/upload')->group(function () {
    Route::post('petAvatar', [Wechat\PetController::class, 'upload']);
});


//Route::post('/login', [Wechat\UserController::class, 'login'])->withoutMiddleware(['auth.wechat']);
//
//
//Route::post('/payment', function (\App\Services\Wechat\MiniProgramPaymentService $service) {
//    return response()->json($service->requestPayment('a', 1, 'b'));
//})->withoutMiddleware(['auth.wechat']);
//
//Route::post('/getRealtimePhoneNumber', [Wechat\UserController::class, 'getRealtimePhoneNumber']);

/*
 *
 * index-index
    "van-cell": "@vant/weapp/cell/index",
    "van-cell-group": "@vant/weapp/cell-group/index",
    "van-row": "@vant/weapp/row/index",
    "van-col": "@vant/weapp/col/index",
    "van-button": "@vant/weapp/button/index",
    "van-action-sheet": "@vant/weapp/action-sheet/index",
    "van-grid": "@vant/weapp/grid/index",
    "van-grid-item": "@vant/weapp/grid-item/index",
    "van-image": "@vant/weapp/image/index"
 */

/*
 * petPenel-index
 * "van-image": "@vant/weapp/image/index",
    "van-tag": "@vant/weapp/tag/index",
    "van-divider": "@vant/weapp/divider/index",
    "van-grid": "@vant/weapp/grid/index",
    "van-grid-item": "@vant/weapp/grid-item/index"
 */

/*
 * my-index-index
 * "van-row": "@vant/weapp/row/index",
    "van-col": "@vant/weapp/col/index",
    "van-button": "@vant/weapp/button/index",
    "van-divider": "@vant/weapp/divider/index",
    "van-grid": "@vant/weapp/grid/index",
    "van-grid-item": "@vant/weapp/grid-item/index"
 */

/*
 * my-pet-list
 * "van-swipe-cell": "@vant/weapp/swipe-cell/index",
    "pet-panel": "/components/PetPanel/index",
    "van-action-sheet": "@vant/weapp/action-sheet/index",
    "van-cell-group": "@vant/weapp/cell-group/index",
    "van-field": "@vant/weapp/field/index",
    "van-radio": "@vant/weapp/radio/index",
    "van-radio-group": "@vant/weapp/radio-group/index",
    "van-uploader": "@vant/weapp/uploader/index",
    "van-button": "@vant/weapp/button/index"
 */