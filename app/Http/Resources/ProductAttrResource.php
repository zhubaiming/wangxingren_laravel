<?php

namespace App\Http\Resources;

class ProductAttrResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'category_title' => $this->category->title,
                    'title' => $this->title
                ],
                'show' => [
//                    'id' => $this->id,
//                    'title' => $this->title,
//                    'type' => $this->type,
//                    'letter' => $this->letter,
//                    'is_sync_attr' => $this->is_sync_attr
                ],
                'default' => []
            },
            false => [
                'value' => $this->id,
                'label' => $this->title
            ]
        };
    }
}
