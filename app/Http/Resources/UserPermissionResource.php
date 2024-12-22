<?php

namespace App\Http\Resources;

class UserPermissionResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'code' => $this->code,
                    'level' => $this->level,
                    'children' => $this->childrenRecursive->count() === 0 ? null : (new BaseCollection($this->childrenRecursive))->additional($this->additional),
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'code' => $this->code,
                    'sort' => $this->sort,
                ],
                'default' => [
                    'code' => $this->code
                ]
            },
            false => [
                'key' => $this->id,
                'label' => $this->title,
//                'disabled' => !($this->childrenRecursive->count() === 0),
                'children' => $this->childrenRecursive->count() === 0 ? null : (new BaseCollection($this->childrenRecursive))->additional($this->additional)
            ]
        };
    }
}
