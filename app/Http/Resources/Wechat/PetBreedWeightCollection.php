<?php

namespace App\Http\Resources\Wechat;

use Illuminate\Http\Request;

class PetBreedWeightCollection extends BaseCollection
{
    public $collects = PetBreedWeightResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (isset($this->additional['self']) && $this->additional['self']) {
            return $this->collection->toArray();
        } else {
            return parent::toArray($request);
        }
    }
}
