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
                    'id' => $this->id,
                    'title' => $this->title,
                    'letter' => $this->letter,
                    'image' => $this->image,
                    'description' => $this->description,
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