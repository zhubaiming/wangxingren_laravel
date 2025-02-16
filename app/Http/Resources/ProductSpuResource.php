<?php

namespace App\Http\Resources;

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
                    'title' => $this->title,
                    'sub_title' => $this->sub_title,
                    'category_title' => $this->category->title,
                    'trademark_title' => $this->trademark->title,
                    'can_use_pet' => $this->id,
                    'sales_count' => $this->order_count,
                    'saleable_color' => $this->transformSaleableColor($this->saleable),
                    'saleable' => $this->saleable
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'sub_title' => $this->sub_title,
                    'category_id' => $this->category_id,
                    'trademark_id' => $this->trademark_id,
                    'can_use_pet' => $this->id,
                    'sales_count' => 0,
                    'saleable' => $this->saleable,
                    'description' => $this->description,
                    'images' => $this->images,
                    'packing_list' => $this->packing_list,
                    'after_service' => $this->after_service,
                    'sort' => $this->sort,
                    'pet_breeds' => $this->spu_breed->pluck('id'),
                ],
                'default' => []
            },
            false => [
                'value' => $this->id,
                'label' => $this->title
                /*
                 * 'value' => [
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
                    'images' => array_map(function ($value) {
                        $value['tabType'] = substr($value['type'], 0, strpos($value['type'], '/'));
                        return $value;
                    }, $this->images),
                    'packing_list' => $this->packing_list,
                    'after_service' => $this->after_service,
                    'pet_breeds' => $this->spu_breed->pluck('id'),
                ],
                 */
            ]
        };

        return $result;
    }

    private function transformSaleableColor($saleable)
    {
        if ($saleable) {
            return ['type' => 'success', 'color' => []];
        }

        return ['type' => 'error', 'color' => []];
    }
}