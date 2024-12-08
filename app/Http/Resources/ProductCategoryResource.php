<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentsResource;

class ProductCategoryResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        $paginate = boolval($this->additional['paginate'] ?? false);

        $result = match ($paginate) {
            true => [
                'value' => $this->id,
                'title' => $this->title,

            ],
            false => [
                'value' => $this->parent_id === 0 ? $this->id : $this->parent_id . '-' . $this->id,
                'label' => $this->title,
                'children' => $this->childrenRecursive->count() === 0 ? null : ProductCategoryResource::collection($this->childrenRecursive)->additional(['paginate' => $paginate])
            ]
        };

        return $result;
    }
}