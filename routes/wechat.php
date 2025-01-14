<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\Wechat;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(['auth.wechat'])->group(function () {
    Route::post('/silentLogin', [Api\Wechat\AuthController::class, 'silentLogin']);
    Route::post('/registerLogin', [Api\Wechat\AuthController::class, 'registerLogin']);

    Route::prefix('system')->group(function () {
        Route::get('/app/index', [Api\Wechat\SystemController::class, 'appIndexShow']);
        Route::get('/company', [Api\Wechat\SystemController::class, 'companyShow']);
    });
});

Route::prefix('system')->group(function () {
    Route::get('/app/poll', [Api\Wechat\SystemController::class, 'appPollIndex']);
    Route::post('/app/poll', [Api\Wechat\SystemController::class, 'appPollStore']);
});

/**
 * 用户
 */
Route::get('/userInfo', [Wechat\UserController::class, 'info']);

/**
 * 宠物 -- 完成
 */
Route::get('/pet_breed/{id}', [Api\Wechat\User\PetController::class, 'breedIndex'])->where(['id' => '^[1-9]\d*']);
Route::apiResource('/pet', Api\Wechat\User\PetController::class);

/**
 * 商品 -- 完成
 */
Route::prefix('product')->group(function () {
    Route::get('/spu', [Api\Wechat\Product\SpuController::class, 'index']);
    Route::get('/spu/titles', [Api\Wechat\Product\SpuController::class, 'searchList']);
    Route::get('/spu/{spu_id}', [Api\Wechat\Product\SpuController::class, 'show']);
    Route::get('/sku', [Api\Wechat\Product\SkuController::class, 'show']);
});


/**
 * 服务/收货 地址
 */
Route::apiResource('/address', Api\Wechat\User\AddressController::class);


/**
 * 预约时间
 */
Route::get('/trade_date/reservation', [Api\Wechat\TradeDateController::class, 'getReservation']);


/**
 * 优惠卷 -- 完成
 */
Route::apiResource('/coupon', Api\Wechat\User\CouponController::class);

/**
 * 订单
 */
Route::get('/order/total', [Api\Wechat\User\OrderController::class, 'total']);
Route::apiResource('/order', Api\Wechat\User\OrderController::class);


/**
 * 上传
 */
Route::prefix('/upload')->group(function () {
    Route::post('petAvatar', [\App\Http\Controllers\UploadController::class, 'clientUserPetAvatar']);
});

/**
 * 支付
 */
/**
 * "" - "POST /wechat_notify/payment/jsapi HTTP/1.1" [30/Dec/2024:07:32:37 +0000] - (wechatpay-signature: pv5xBCsPCv8hfhUzg5MEXs9v0GFGYOWF92KjQBJZlLkjxxA/lEicXxE4YnN5rwNndMhuqdbFq1DX9Y/LdEysMikwFQf9gJLUBxaiqYhTqwi/j1km5UYChRwfKJDRBWF+v5xLeB+WuAx71L/fJi3BbQSLronqJkJjhuokagp3aksr5O66IC0l22QhqBaoRQSlaH3r8k0hyZzxUlqh49dgUZbXVsX63U5qNUddYdKtC11iLxtXA9FKA/U8/5ahZ1IKImEENlAH9/BjNYQie+blU/ojgEtP08P9IiWDytHVvejfvqWZLmo9/AWRF8vckzsMTPwkQC3y+i8BDVjR3DzgRw==) - (wechatpay-nonce: tUk8R6HclVWTvmfOiXOtk3gTnLJv1Owt) - (wechatpay-timestamp: 1735543956) - (wechatpay-serial: 2A9256DD5CB30E9FD7482299A7C36670BFC13900) - (request-id: ) - "{\"id\":\"35fade70-eb26-57cc-84ac-550cd792de9f\",\"create_time\":\"2024-12-30T15:32:36+08:00\",\"resource_type\":\"encrypt-resource\",\"event_type\":\"TRANSACTION.SUCCESS\",\"summary\":\"支付成功\",\"resource\":{\"original_type\":\"transaction\",\"algorithm\":\"AEAD_AES_256_GCM\",\"ciphertext\":\"/OZM2fmdQF7YFdahOuNvcS2ZW8IDhLygSY/ltlXNBIk5sPNxj5QyFok8/kD//URt9sybRW2qaxuDdNdY5OlUf1EX2w0n3Ifv/J2Y6pbuexGZdKJ8uKNG9RIgthz0j9U/K7adVFqfENNx+UWqdMOgJddNJ4vLU8aa3oOyAXf3PaZ+6hpaHbNC6/ketBBW42mjUakIP8rWcHaas/UtkE3gzkM/hA5RrtawQT/sZcZFx9nDkd1bv5TAihJ5Scp3AGXaVFO5uah4wycnhopaq53KWT+/D8u3+C41vGx9xdrGuxO/hHIISgpG0ysCJu4M1r1V04rz3mb+IYQ1zx13sTkLeeTgUDy3Hzr2IGlXH5nhAcfyPDovpjj2Mr02witNLQai+l1iV6d9Jld7Sxp9J0L7HEcIByATkEj2YG8INmP0GYRa2cjtS1tVsqK30AXjK+htVH7NYfxA0fSjDqbSi2tslqw/wiF90r9pvyH4jlkDUp5eECcYFocrWg47rG98e4o9vs6zutFG0OMnzJscnaAsuxyjizPv8w1s1glulAVJny12erSgbmHmjkI0TcUOm41ddx0=\",\"associated_data\":\"transaction\",\"nonce\":\"zbCwOIWVvwnj\"}}"
 */
//Route::post('/payment', function (\App\Services\Wechat\MiniProgramPaymentService $service) {
//    return response()->json($service->requestPayment('a', 1, 'b'));
//})->withoutMiddleware(['auth.wechat']);
//
//Route::post('/getRealtimePhoneNumber', [Wechat\UserController::class, 'getRealtimePhoneNumber']);