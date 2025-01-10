<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;
use Carbon\Carbon;

class ProductSpuResource extends CommentsResource
{
    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        $result = match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'category_id' => $this->category_id,
                    'title' => $this->title,
                    'sub_title' => $this->sub_title,
                    'min_price' => applyIntegerToFloatModifier($this->skus_min_price),
                    'sales_volume' => '缺销量',
                    'cover' => $this->images[0] ?? null,
                    'is_new' => !Carbon::parse($this->created_at)->lt(Carbon::now()->subHours(12)),
                    'order_count' => $this->order_count
                ],
                'show' => [
                    'id' => $this->id,
                    'trademark_id' => $this->trademark_id,
                    'category_id' => $this->category_id,
                    'title' => $this->title,
                    'sub_title' => $this->sub_title,
                    'min_price' => applyIntegerToFloatModifier($this->skus_min_price),
                    'order_count' => $this->order_count,
                    'score' => 5,
                    'is_new' => !Carbon::parse($this->created_at)->lt(Carbon::now()->subHours(12)),
                    'description' => $this->description,
                    'images' => $this->images,
                    'packing_list' => $this->packing_list,
                    'after_service' => $this->after_service,
                    'pet_breeds' => $this->spu_breed->pluck('id'),
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