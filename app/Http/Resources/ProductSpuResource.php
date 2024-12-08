<?php

namespace App\Http\Resources;

class ProductSpuResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'trademark_title' => $this->trademark->title,
            'category_title' => $this->id,
            'can_use_pet' => $this->id,
            'saleable_color' => $this->transformSaleableColor($this->saleable),
            'saleable' => $this->saleable,
        ];
    }

    private function transformSaleableColor($saleable)
    {
        if ($saleable) {
            return ['type' => 'success', 'color' => []];
        }

        return ['type' => 'error', 'color' => []];
    }
}