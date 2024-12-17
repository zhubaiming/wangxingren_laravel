<?php

namespace App\Http\Resources;

class ProductTrademarkResource extends CommentsResource
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
                    'letter' => $this->letter,
                    'image' => $this->image,
                    'created_at' => $this->created_at,
                ],
                'show' => [
//                    'id' => $this->id,
//                    'title' => $this->title,
//                    'update_by' => $this->update_by,
//                    'created_at' => $this->created_at,
//                    'updated_at' => $this->updated_at
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