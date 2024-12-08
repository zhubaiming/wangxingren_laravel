<?php

namespace App\Http\Resources;

class PetBreedResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title
        ];
    }
}