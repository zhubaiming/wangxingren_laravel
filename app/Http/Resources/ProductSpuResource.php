<?php

namespace App\Http\Resources;

class ProductSpuResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        $format = $this->additional['format'] ?? 'default';

        $result = match ($format) {
            'index' => [
                'id' => $this->id,
                'title' => $this->title,
                'sub_title' => $this->sub_title,
                'duration' => floatval(applyIntegerToFloatModifier($this->duration, '10', 1)),
                'category_title' => $this->category->title,
                'trademark_title' => $this->trademark->title,
                'can_use_pet' => $this->id,
                'sales_count' => 0,
                'saleable_color' => $this->transformSaleableColor($this->saleable),
                'saleable' => $this->saleable,
            ],
            'show' => [
                'id' => $this->id,
                'title' => $this->title,
                'sub_title' => $this->sub_title,
                'duration' => floatval(applyIntegerToFloatModifier($this->duration, '10', 1)),
                'category_id' => $this->category_id,
                'trademark_id' => $this->trademark_id,
                'can_use_pet' => $this->id,
                'sales_count' => 0,
                'saleable' => $this->saleable,
                'description' => $this->detail->description,
                'images' => $this->detail->images,
                'packing_list' => $this->detail->packing_list,
                'after_service' => $this->detail->after_service
            ],
            'default' => []
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