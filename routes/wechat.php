<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\Wechat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

});

/**
 * 用户
 */
Route::post('/registerLogin', [Wechat\UserController::class, 'registerLogin']);
Route::get('/userInfo', [Wechat\UserController::class, 'info']);

/**
 * 宠物
 */
Route::get('/pet_category/{id}', [Wechat\UserPetController::class, 'category'])->where(['id' => '^[1-9]\d*']);
//Route::apiResource('pet', Wechat\UserPetController::class);
Route::apiResource('/pet', Api\Wechat\User\PetController::class);

/**
 * 商品 -- 完成
 */
Route::prefix('product')->group(function () {
    Route::get('/spu', [Api\Wechat\Product\SpuController::class, 'index']);
    Route::get('/spu/{spu_id}', [Api\Wechat\Product\SpuController::class, 'show']);
//    Route::get('/category/{category_id}/spu',[])
    Route::get('/sku', [Api\Wechat\Product\SkuController::class, 'show']);
});


/**
 * 服务/收货 地址
 */
//Route::apiResource('/address', Wechat\UserAddressController::class);
Route::apiResource('/address', Api\Wechat\User\AddressController::class);


/**
 * 预约时间
 */
Route::get('/trade_date/reservation', [Api\Wechat\TradeDateController::class, 'getReservation']);


/**
 * 优惠卷
 */
//Route::apiResource('/coupon', Wechat\UserCouponController::class);
Route::apiResource('/coupon', Api\Wechat\User\CouponController::class);

/**
 * 订单
 */
//Route::get('/order/total', [Wechat\UserOrderController::class, 'total']);
//Route::apiResource('/order', Wechat\UserOrderController::class);
Route::get('/order/total', [Api\Wechat\User\OrderController::class, 'total']);
Route::apiResource('/order', Api\Wechat\User\OrderController::class);


Route::prefix('goods_category')->group(function () {
    Route::get('/{parent_id?}', [Wechat\GoodCategoryController::class, 'index'])->where(['parent_id' => '^[1-9]\d*'])->withoutMiddleware(['auth.wechat']); // 如果传递了 parent_id，则从 parent_id 开始获取分类树；否则从顶级开始获取分类树
    Route::apiResource('/{cid}/goods', Wechat\GoodsSpuController::class)->only(['index', 'show'])->where(['cid' => '^[1-9]\d*', 'good' => '^[1-9]\d*'])->withoutMiddleware(['auth.wechat']);  // 列出某个指定分类的所有商品
    Route::get('/{cid}/goods/{gid}/sku', [Wechat\GoodsSkuController::class, 'show'])->where(['cid' => '^[1-9]\d*', 'gid' => '^[1-9]\d*']);  // 查询具体sku
    Route::get('/{cid}/goods/{gid}/service_times', [Wechat\GoodsServieTimeController::class, 'index'])->where(['cid' => '^[1-9]\d*', 'gid' => '^[1-9]\d*']);  // 查询具体sku
});


/**
 * 上传
 */
Route::prefix('/upload')->group(function () {
    Route::post('petAvatar', [Wechat\UserPetController::class, 'upload']);
});

/**
 * 支付
 */
//Route::post('/payment', function (\App\Services\Wechat\MiniProgramPaymentService $service) {
//    return response()->json($service->requestPayment('a', 1, 'b'));
//})->withoutMiddleware(['auth.wechat']);
//
//Route::post('/getRealtimePhoneNumber', [Wechat\UserController::class, 'getRealtimePhoneNumber']);