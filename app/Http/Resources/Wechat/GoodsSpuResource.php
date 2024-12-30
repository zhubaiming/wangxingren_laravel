<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class GoodsSpuResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $format = $this->additional['format'] ?? 'default';

        $result = match ($format) {
            'index' => [
                'id' => $this->id,
                'category_id' => $this->category_id,
                'title' => $this->title,
                'sub_title' => $this->sub_title,
                'min_price' => applyIntegerToFloatModifier($this->skus_min_price),
                'sales_volume' => '缺销量',
                'cover' => '缺封面',
                'is_new' => !(43200 > intval(bcsub(strtotime(date('Y-m-d H:i:s')), strtotime($this->created_at), 0)))
            ],
            'show' => [
                'id' => $this->id,
                'category_id' => $this->category_id,
                'title' => $this->title,
                'sub_title' => $this->sub_title,
                'min_price' => applyIntegerToFloatModifier($this->skus_min_price),
                'sales_volume' => '缺销量',
                'score' => '缺评分',
                'is_new' => !(43200 > intval(bcsub(strtotime(date('Y-m-d H:i:s')), strtotime($this->created_at), 0))),
                'description' => $this->detail->description,
                'images' => $this->detail->images ?? [
                        'https://crm.misswhite.com.cn/storage/topic/6459cc63daedf.jpg',
                        'https://crm.misswhite.com.cn/storage/topic/6459cc6b1745a.jpg',
                        'https://crm.misswhite.com.cn/storage/topic/6459cc78ddfb0.jpg',
                        'https://crm.misswhite.com.cn/storage/topic/6459cc7dd5710.jpg'
                    ],
                'packing_list' => $this->detail->packing_list,
                'after_service' => $this->detail->after_service,
                'spec_groups' => GoodsSpecGroupResource::collection($this->specGroups),
//                'sku_ids'=>
//                'skus' => ProductSkuResource::collection($this->skus),
//                'service_times' => (new GoodsServiceTimeCollection($this->serviceTimes))->additional(['self' => true]),
//                'service_times' => GoodsServiceTimeResource::collection($this->serviceTimes),
            ],
            'default' => []
        };

        return $result;
    }
}
