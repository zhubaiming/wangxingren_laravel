<?php

namespace App\Http\Resources;

use App\Enums\PetCategoryEnum;

class PetBreedResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'type' => PetCategoryEnum::from($this->type)->name(),
                    'is_sync_attr' => $this->is_sync_attr
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'type' => $this->type,
                    'letter' => $this->letter,
                    'is_sync_attr' => $this->is_sync_attr
                ],
                'default' => []
            },
            false => [
                'value' => $this->id,
                'label' => $this->title
            ]
        };
    }
}