<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class GoodsCategoryResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $children_recursive = GoodsCategoryResource::collection($this->childrenRecursive);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'sort' => $this->sort,
            'children_recursive' => $this->when(!$children_recursive->isEmpty(), $children_recursive),
        ];
    }
}
