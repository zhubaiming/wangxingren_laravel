<?php

namespace App\Http\Resources;

class ProductTrademarkResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        $paginate = boolval($this->additional['paginate'] ?? false);

        $result = match ($paginate) {
            true => [
                'id' => $this->id,
                'title' => $this->title,
                'letter' => $this->letter,
                'image' => $this->image,
                'created_at' => $this->created_at,
            ],
            false => [
                'value' => $this->id,
                'label' => $this->title
            ]
        };

        return $result;
    }
}