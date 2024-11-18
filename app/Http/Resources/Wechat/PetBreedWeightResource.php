<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class PetBreedWeightResource extends CommentsResource
{
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title
        ];
    }
}
