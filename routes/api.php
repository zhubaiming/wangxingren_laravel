<?php

use App\Http\Controllers;
use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\V1;
use Illuminate\Support\Facades\Route;

Route::get('/testEvent', [Controllers\TestController::class, 'testEvent']);
Route::get('/message/send', [Controllers\TestController::class, 'send'])->withoutMiddleware([\App\Http\Middleware\ApiAuthMiddleware::class]);


Route::prefix('v1')->group(function () {
    Route::apiResource('dict', Controllers\DictController::class);


    Route::get('captcha', function () {
        // 创建一个新的 XML 文档
        $dom = new DOMDocument('1.0', 'UTF-8');

        // 创建 <svg> 元素
        $svg = $dom->createElementNS('http://www.w3.org/2000/svg', 'svg');
        // 设置宽高
        $svg->setAttribute('width', '120');
        $svg->setAttribute('height', '48');


//        // circle 元素
//        // 创建 <circle> 元素
//        $circle = $dom->createElement('circle');
//        // 圆心坐标
//        $circle->setAttribute('cx', 100);
//        $circle->setAttribute('cy', 100);
//        // 半径
//        $circle->setAttribute('r', 50);
//        // 填充色
//        $circle->setAttribute('fill', 'red');
//        // 将 <circle> 元素添加到 <svg> 元素中
//        $svg->appendChild($circle);


        // text 元素
        $text = $dom->createElement('text');
        $text->setAttribute('x', '0');
        $text->setAttribute('y', '0');
        $text->setAttribute('fill', 'black');
        $text->setAttribute('font-family', 'Arial');
        $text->setAttribute('font-size', '20');
        $text->setAttribute('font-style', 'oblique');
        $text->setAttribute('font-weight', '700');
        $text->nodeValue = 'RSJxW';
        $svg->appendChild($text);


        // 将 <svg> 元素添加到 XML 文档中
        $dom->appendChild($svg);

        return response()->json([
            'payload' => $dom->saveXML()
        ]);

        dd($dom->saveXML());

        // 设置 HTTP 头来制定内容类型为SVG
        header('Content-type: image/svg+xml');

        // 输出SVG内容
        echo $dom->saveXML();

        exit(0);
    });


    Route::prefix('broadcasting')->group(function () {
        Route::post('/auth', [Admin\AuthController::class, 'authenticate']);
    });

    // 账户操作
    Route::prefix('user')->group(function () {
        Route::controller(Admin\AuthController::class)->group(function () {
            Route::post('/login', 'login');
            Route::post('/logout', 'logout');
            Route::put('/resetPasswd/{id}', 'resetPasswd');
            Route::put('/user', 'updateSelf');
            Route::get('/info', 'info');
            Route::put('/batchToggle', 'batchToggle');
        });
//        Route::get('info', [V1\UserController::class, 'info']);
        Route::apiResource('role', Admin\AuthRoleController::class);
        Route::apiResource('permission', V1\UserPermissionController::class);
    });
    Route::apiResource('/user', Admin\AuthController::class);


    // 营业日期 - 已完成
    Route::get('/tradeDate/reservation', [Admin\TradeDateController::class, 'getReservation']);
    Route::apiResource('/tradeDate', Admin\TradeDateController::class)->only(['index', 'update']);
    // 营业车辆 - 已完成
    Route::apiResource('/serviceCar', V1\ServiceCarController::class)->only(['index']);

    // 系统相关设置 - 完成
    Route::prefix('system')->group(function () {
        Route::get('/app/index', [Admin\SystemController::class, 'appIndexShow']);
        Route::put('/app/index', [Admin\SystemController::class, 'appIndexUpdate']);
        Route::get('/app/poll', [Admin\SystemController::class, 'appPollIndex']);
        Route::put('/app/poll', [Admin\SystemController::class, 'appPollUpdate']);
        Route::get('/company', [Admin\SystemController::class, 'companyIndex']);
        Route::put('/company', [Admin\SystemController::class, 'companyUpdate']);
    });

    Route::post('/coupon/{id}/user', [Admin\Coupon\CouponController::class, 'issueCouponToUser'])->withoutMiddleware('api');
    Route::apiResource('/coupon', Admin\Coupon\CouponController::class);
    Route::prefix('clientUser')->group(function () {
        Route::apiResource('/pet', Admin\ClientUser\PetController::class);
        Route::apiResource('/address', Admin\ClientUser\AddressController::class);
    });
    Route::apiResource('/clientUser', Admin\ClientUser\UserController::class);


    Route::get('/home', [V1\HomeController::class, 'info']);
    Route::apiResource('/order', Admin\ClientUser\OrderController::class);
    // 商品
    Route::prefix('product')->group(function () {
        // 商品品牌 - 已完成
        Route::apiResource('/trademark', V1\ProductTrademarkController::class);
        Route::delete('/trademark', [V1\ProductTrademarkController::class, 'batchDestroy']);

        // 商品分类 - 已完成
        Route::get('/category/{category_id}/pet_breed', [V1\PetBreedController::class, 'category_breed']);
        Route::delete('/category', [V1\ProductCategoryController::class, 'batchDestroy']);
        Route::apiResource('/category', V1\ProductCategoryController::class);

        // spu - 已完成
        Route::get('/spu/{spu_id}/pet_breed', [V1\PetBreedController::class, 'spu_breed']);
        Route::put('/spu', [Admin\Product\SpuController::class, 'batchUpdate']);
        Route::delete('/spu', [Admin\Product\SpuController::class, 'batchDestroy']);
        Route::apiResource('/spu', Admin\Product\SpuController::class);


        // sku
        Route::apiResource('/sku', Admin\Product\SkuController::class);
    });


    Route::prefix('pet')->group(function () {
        Route::apiResource('breed', V1\PetBreedController::class);
    });


    // 上传
    Route::prefix('upload')->withoutMiddleware('api')->group(function () {
        // 富文本
        Route::post('reach_text', [Controllers\UploadController::class, 'reachText']);
        Route::prefix('app')->group(function () {
            Route::post('banner', [Controllers\UploadController::class, 'appBanner']);
        });
        Route::post('spu', [Controllers\UploadController::class, 'spuImages']);
        Route::prefix('user')->group(function () {
            Route::post('avatar', [Controllers\UploadController::class, 'userAvatar']);
        });
        Route::prefix('company')->group(function () {
            Route::post('info', [Controllers\UploadController::class, 'companyInfo']);
        });
    });
});