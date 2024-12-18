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
                    'id' => $this->id,
                    'title' => $this->title,
                    'trademark_title' => $this->trademarks->pluck('title'),
                    'children' => $this->childrenRecursive->count() === 0 ? null : (new BaseCollection($this->childrenRecursive))->additional($this->additional)
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'sort' => $this->sort,
                    'description' => $this->description,
                    'trademarks' => $this->trademarks->pluck('id')
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