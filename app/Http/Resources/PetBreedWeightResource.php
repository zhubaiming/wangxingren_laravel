<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentsResource;

class PetBreedWeightResource extends CommentsResource
{

    /**
     * @inheritDoc
     */
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'breed_id' => $this->breed->id,
            'breed_title' => $this->breed->title,
        ];
    }
}