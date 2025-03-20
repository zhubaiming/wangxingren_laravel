<?php

namespace App\Http\Resources;

use App\Enums\GenderEnum;
use App\Enums\PetCategoryEnum;
use App\Enums\PetWeightRangeEnum;

class ClientUserPetResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                ],
                'show' => [
                    'id' => $this->id,
                ],
                'default' => []
            },
            false => [
                'value' => $this->id,
                'label' => $this->name . '(' . $this->breed_title . '、' . PetCategoryEnum::from($this->breed_type)->name() . '、' . GenderEnum::from($this->gender)->name('animal') . ')' . '【' . PetWeightRangeEnum::from(applyFloatToIntegerModifier($this->weight))->name() . '】'
            ]
        };
    }
}
