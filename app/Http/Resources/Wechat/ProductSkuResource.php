<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class ProductSkuResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        $result = match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
//                    'category_id' => $this->category_id,
//                    'title' => $this->title,
//                    'sub_title' => $this->sub_title,
//                    'min_price' => applyIntegerToFloatModifier($this->skus_min_price),
//                    'sales_volume' => '缺销量',
//                    'cover' => $this->images[0] ?? null,
////                    'is_new' => !(43200 > intval(bcsub(strtotime(date('Y-m-d H:i:s')), strtotime($this->created_at), 0))),
//                    'is_new' => !Carbon::parse($this->created_at)->lt(Carbon::now()->subHours(12)),
//                    'order_count' => $this->order_count
                ],
                'show' => [
                    'id' => $this->id,
                    'duration' => $this->duration,
                    'stock' => $this->stock,
                    'price' => $this->price,
                    'price_conv' => $this->price_conv,
                ],
                'default' => []
            },
            false => [
                'value' => $this->id,
                'label' => $this->title
            ]
        };

        return $result;
    }
}
