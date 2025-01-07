<?php

namespace App\Http\Resources;

class UserRoleResource extends CommentsResource
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
                    'can_delete' => $this->can_delete,
                    'updated_by' => $this->updated_by,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'permissions' => $this->permissions->pluck('id'),
                ],
                'default' => []
            },
            false => [
                'id' => $this->id,
                'title' => $this->title
            ]
        };
    }
}
