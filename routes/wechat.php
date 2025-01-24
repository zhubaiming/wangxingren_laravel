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

    /**
     * 商品
     */
    Route::prefix('product')->group(function () {
        Route::get('/spu', [Api\Wechat\Product\SpuController::class, 'index']);
        Route::get('/spu/titles', [Api\Wechat\Product\SpuController::class, 'searchList']);
        Route::get('/spu/{spu_id}', [Api\Wechat\Product\SpuController::class, 'show']);
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
 * 宠物
 */
Route::get('/pet_breed/{id}', [Api\Wechat\User\PetController::class, 'breedIndex'])->where(['id' => '^[1-9]\d*']);
Route::apiResource('/pet', Api\Wechat\User\PetController::class);

/**
 * 商品
 */
Route::prefix('product')->group(function () {
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