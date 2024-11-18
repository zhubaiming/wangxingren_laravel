<?php

namespace App\Http\Resources\Wechat;

class GoodsSpuCollection extends BaseCollection
{
    public $collects = GoodsSpuResource::class;

//    public function toArray($request): array
//    {
//        $collects = $this->collects;
//        $additionalData = $this->additional;
//
//        $this->collection = $this->collection->map(function ($spu) use ($collects, $additionalData) {
//            return (new $collects($spu))->additional($additionalData);
//        });
//
//        return parent::toArray($request);
//    }
}
