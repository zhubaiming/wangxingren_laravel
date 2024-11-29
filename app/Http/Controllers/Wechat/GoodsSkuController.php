<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\GoodsSkuResource;
use App\Services\GoodsSkuService;
use Illuminate\Http\Request;

class GoodsSkuController extends Controller
{
    public function __construct(GoodsSkuService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the specified resource.
     */
    public function show($category_id, $spu_id, Request $request)
    {
        // todo: 需要对入参进行前置校验

        ['specGroupId' => $spec_group_id, 'breedId' => $breed_id, 'weightId' => $weight_id] = $request->input();

        // todo: 需要对入参进行前置校验

        // 查询条件
        $conditions = ['spu_id' => $spu_id, 'spec_group_id' => $spec_group_id, 'spec_values->breed_id' => $breed_id];
        if (!in_array($weight_id, ['null', 'undefined'])) $conditions['spec_values->weight_id'] = $weight_id;

        // 作用域
        $scopes = ['enable' => true]; // 调用 popular 作用域

        // 要查询的字段
        $fields = ['id', 'price'];

        $payload = $this->service->find($conditions, $scopes, fields: $fields);

        return $this->success(new GoodsSkuResource($payload));
    }
}
