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

    private function transformSaleableColor($saleable)
    {
        if ($saleable) {
            return ['type' => 'success', 'color' => []];
        }

        return ['type' => 'error', 'color' => []];
    }
}