<?php

namespace App\Services;

class GoodsCategoryService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\SysGoodsCategory');

        $this->events = app('events');
    }

//    public function getAllList(int $parent_id = 0)
//    {
//        return $this->model->ofIsParent(0 === $parent_id, $parent_id)->with(['childrenRecursive'])->get();
//    }

//    public function createOne(array $data)
//    {
//        list('spu' => $spu, 'detail' => $detail, 'sku' => $sku) = $data;
//        $this->model->create()
//        $brand_id = $request->input('brand_id');
//        $data = $request->input();
//        unset($data['brand_id']);
//        $model = SysGoodsCategorySeeder::create($data);
//        $model->brands()->attach($brand_id);
//        $model->refresh();
//        return response()->json($model);
//    }

//    public function createOne(array $spu, array $detail, array $sku)
//    {
//        $spu = $this->model->create($spu);
//
//        $spu->detail()->create($detail);
//
//        $spu->sk
//    }
}