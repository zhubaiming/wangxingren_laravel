<?php

namespace App\Http\Resources;

class ServiceCarResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        $result = match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'title' => $this->title
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
            },
            false => [
                'value' => $this->id,
                'label' => $this->title
            ]
        };

        return $result;
    }
}
