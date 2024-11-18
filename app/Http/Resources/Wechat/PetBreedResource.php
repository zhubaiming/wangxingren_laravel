<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class PetBreedResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $weights = (new PetBreedWeightCollection($this->specValue->weights))->additional(['self' => true]);

        return [
            'id' => $this->specValue->id,
            'title' => $this->specValue->title,
            'weights' => $this->when(!$weights->isEmpty(), $weights)
        ];
    }
}
