<?php

namespace App\Http\Resources;

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
                    'spu_id' => $this->spu_id,
                    'breed_id' => $this->breed_id,
                    'weight_min' => $this->weight_min,
                    'weight_max' => $this->weight_max,
                    'duration' => $this->duration,
                    'stock' => $this->stock,
                    'price' => $this->price
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'sub_title' => $this->sub_title,
//                    'duration' => floatval(applyIntegerToFloatModifier($this->duration, '10', 1)),
                    'category_id' => $this->category_id,
                    'trademark_id' => $this->trademark_id,
                    'can_use_pet' => $this->id,
                    'sales_count' => 0,
                    'saleable' => $this->saleable,
                    'description' => $this->description,
                    'images' => $this->images,
                    'packing_list' => $this->packing_list,
                    'after_service' => $this->after_service,
                    'pet_breeds' => $this->spu_breed->pluck('id'),
//                'description' => $this->detail->description,
//                'images' => $this->detail->images,
//                'packing_list' => $this->detail->packing_list,
//                'after_service' => $this->detail->after_service
                ],
                'default' => []
            },
            false => match ($format) {
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
            }
        };

        return $result;
    }
}
