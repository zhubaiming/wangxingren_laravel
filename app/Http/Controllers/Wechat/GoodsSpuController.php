<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wechat\GoodsSpuResource;
use App\Services\GoodsSpuService;
use Illuminate\Http\Request;

class GoodsSpuController extends Controller
{
    public function __construct(GoodsSpuService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($category_id, Request $request)
    {
        // todo: 需要对入参进行前置校验

        // 查询条件
        $conditions = ['brand_id' => 325403, 'category_id' => $category_id];

        // 作用域
        $scopes = ['saleable' => true]; // 调用 popular 作用域

        // 关联聚合
        $aggregates = [
            'skus' => ['min' => 'price']
        ];

        // 要查询的字段
        $fields = ['id', 'category_id', 'title', 'sub_title', 'created_at'];

        // 排序
        $orderBy = [];

        $payload = $this->service->getList($conditions, $scopes, aggregates: $aggregates, fields: $fields, paginate: true);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\Wechat\GoodsSpuResource', 'format' => __FUNCTION__]);
    }

    /**
     * Display the specified resource.
     */
    public function show($category_id, $id, Request $request)
    {
        // todo: 需要对入参进行前置校验

        // 查询条件
        $conditions = ['brand_id' => 325403, 'category_id' => $category_id, 'id' => $id];

        // 作用域
        $scopes = ['saleable' => true]; // 调用 popular 作用域

        // 关联关系
        $relations = [
            'detail:spu_id,description,images,packing_list,after_service',
            'specGroups' => function ($q1) use ($id) {
                $q1->with(['slotSpecs' => function ($q2) use ($id) {
                    $q2->where(['spu_id' => $id])->with(['specValue.weights' => function ($q3) {
                        $q3->withoutGlobalScope('defaultSort');
                    }]);
                }]);
            }
        ];

        // 关联聚合
        $aggregates = [
            'skus' => ['min' => 'price']
        ];

        // 要查询的字段
        $fields = ['id', 'category_id', 'title', 'sub_title', 'created_at'];

        $payload = $this->service->find($conditions, $scopes, $relations, $aggregates, $fields);

        return $this->success((new GoodsSpuResource($payload))->additional(['format' => __FUNCTION__]));
    }
}
