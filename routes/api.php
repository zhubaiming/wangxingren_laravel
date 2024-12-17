<?php

use App\Http\Controllers;
use App\Http\Controllers\V1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/testEvent', [Controllers\TestController::class, 'testEvent']);
Route::get('/message/send', [Controllers\TestController::class, 'send']);

Route::post('/shop_service', function (Request $request, \App\Services\ShopServiceService $service) {
    dd($service->createServiceInfo($request->post(), false));
});


Route::prefix('goods')->group(function () {
    Route::apiResource('/category', V1\GoodCategoryController::class);
});

Route::prefix('v1')->group(function () {
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
    // 账户操作
    Route::prefix('user')->group(function () {
        Route::post('login', [V1\UserController::class, 'login']);
        Route::get('info', [V1\UserController::class, 'info']);
        Route::post('logout', [V1\UserController::class, 'logout']);
        Route::apiResource('role', V1\UserRoleController::class);
        Route::put('batchToggle', [V1\UserController::class, 'batchToggle']);
        Route::put('resetPasswd/{id}', [V1\UserController::class, 'resetPasswd']);
        Route::apiResource('menu', V1\UserMenuController::class);
        Route::apiResource('permission', V1\UserPermissionController::class);
    });
    Route::apiResource('/user', V1\UserController::class);
    Route::put('/user', [V1\UserController::class, 'updateSelf']);


    // 营业日期 - 已完成
    Route::apiResource('/tradeDate', V1\TradeDateController::class)->only(['index', 'update']);
    // 营业车辆 - 已完成
    Route::apiResource('/serviceCar', V1\ServiceCarController::class)->only(['index']);
    // 设置相关 - 已完成
    Route::prefix('setting')->group(function () {
        Route::apiResource('/company', V1\CompanyController::class)->only(['index', 'update']);
    });


    Route::get('/home', [V1\HomeController::class, 'info']);
    Route::apiResource('/clientUser', V1\ClientUserController::class);
    Route::apiResource('/order', V1\OrderController::class);
    // 商品
    Route::prefix('product')->group(function () {
        Route::apiResource('/trademark', V1\ProductTrademarkController::class);
        Route::apiResource('/category', V1\ProductCategoryController::class);
        Route::get('/category/{category_id}/pet_breed', [V1\PetBreedController::class, 'category_breed']);

        // spu
        Route::apiResource('/spu', V1\ProductSpuController::class);
        Route::put('/spu', [V1\ProductSpuController::class, 'batchUpdate']);
        Route::delete('/spu', [V1\ProductSpuController::class, 'batchDestroy']);


        Route::apiResource('/serviceTime', V1\ProductServiceTimeController::class);
    });

//    Route::prefix('sreviceTime')->group(function () {
//        Route::get('/', [V1\ServiceTimeController::class, 'index']);
//
//        Route::get('date', [V1\ServiceTimeController::class, 'dateList']);
//        Route::post('checkDate', [V1\ServiceTimeController::class, 'checkDate']);
//        Route::post('/', [V1\ServiceTimeController::class, 'store']);
//    });


    Route::prefix('pet')->group(function () {
        Route::apiResource('breed', V1\PetBreedController::class);
        Route::apiResource('weight', V1\PetWeightController::class);
    });

    Route::apiResource('/goods_category', V1\GoodCategoryController::class);
    Route::apiResource('/goods', V1\GoodsController::class);

    // 上传
    Route::prefix('upload')->group(function () {
        // 公司信息
        Route::prefix('companyInfo')->group(function () {
            Route::post('info', [Controllers\UploadController::class, 'companyInfo']);
        });
        // 富文本
        Route::post('reach_text', [Controllers\UploadController::class, 'reachText']);
        // 用户
        Route::prefix('user')->group(function () {
            Route::post('avatar', [Controllers\UploadController::class, 'userAvatar']);
        });


        Route::post('spu', function (Request $request) {
            return response()->json([
                'code' => 200,
                'message' => __('http_response.success'),
                'payload' => (new \App\Services\UploadFileService())->spuImage($request->file('file'))
            ]);
        });
        Route::post('co', function (Request $request) {
            return response()->json([
                'code' => 200,
                'message' => __('http_response.success'),
                'payload' => (new \App\Services\UploadFileService())->spuImage($request->file('file'))
            ]);
        });
    });
});