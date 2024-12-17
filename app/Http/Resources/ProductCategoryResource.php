<?php

namespace App\Http\Resources;

class ProductCategoryResource extends CommentsResource
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
                    'value' => $this->id,
                    'title' => $this->title,
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
                'label' => $this->title,
                'children' => $this->childrenRecursive->count() === 0 ? null : (new BaseCollection($this->childrenRecursive))->additional($this->additional)
            ]
        };

        return $result;
    }
}