<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\GoodsServiceTimeCollection;
use App\Services\GoodsSpuService;

class GoodsServieTimeController extends Controller
{
    public function __construct(GoodsSpuService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($category_id, $spu_id)
    {
        // todo: 需要对入参进行前置校验

        // 查询条件
        $conditions = ['brand_id' => 325403, 'category_id' => $category_id, 'id' => $spu_id];

        // 作用域
        $scopes = ['saleable' => true]; // 调用 popular 作用域

        // 关联关系
        $relations = ['serviceTimes' => function ($query) {
            $query->enable();
        }];

        $payload = $this->service->find($conditions, $scopes, $relations);

//        return response()->json($payload->serviceTimes);

        return (new GoodsServiceTimeCollection($payload->serviceTimes));
    }
}
