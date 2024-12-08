<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class GoodsSpecGroupResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $spec_values = PetBreedResource::collection($this->slotSpecs);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'spec_values' => $this->when(!$spec_values->isEmpty(), $spec_values)
        ];
    }
}
