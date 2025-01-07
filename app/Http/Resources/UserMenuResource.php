<?php

namespace App\Http\Resources;

class UserMenuResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
//                    'id' => $this->id,
//                    'title' => $this->title,
//                    'updated_by' => $this->updated_by,
//                    'created_at' => $this->created_at,
//                    'updated_at' => $this->updated_at
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
                'key' => $this->id,
                'label' => $this->title,
                'children' => $this->childrenRecursive->count() === 0 ? null : (new BaseCollection($this->childrenRecursive))->additional($this->additional)
            ]
        };
    }
}
