<?php

namespace App\Http\Resources;

class DictResource extends CommentsResource
{

    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'label' => $this->label,
                    'value' => $this->value,
                    'name' => $this->name ?? $this->enum_name::from($this->value)->name()
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'permissions' => $this->permissions->pluck('id'),
                ],
                'default' => []
            },
            false => [
                'value' => $this->value,
                'label' => $this->name ?? $this->enum_name::from($this->value)->name()
            ]
        };
    }
}
