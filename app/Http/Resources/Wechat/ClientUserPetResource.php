<?php

namespace App\Http\Resources\Wechat;

use App\Enums\GenderEnum;
use App\Enums\PetCategoryEnum;
use App\Enums\PetWeightRangeEnum;
use App\Http\Resources\CommentsResource;

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
                    'breed_id' => $this->breed_id,
                    'breed_title' => $this->breed_title,
                    'name' => $this->name,
                    'breed_type' => $this->breed_type,
                    'breed_type_conv' => strtoupper(PetCategoryEnum::from($this->breed_type)->name),
                    'gender' => $this->gender,
                    'gender_conv' => GenderEnum::from($this->gender)->name('animal'),
                    'weight' => $this->weight,
                    'weight_conv' => PetWeightRangeEnum::from($this->weight)->name(),
                    'birth' => $this->birth,
                    'age' => calculateAge($this->birth, 'Y-m'),
                    'color' => $this->color,
                    'avatar' => $this->avatar,
                    'remark' => $this->remark,
                    'is_sterilization' => $this->is_sterilization,
                    'is_default' => $this->is_default
                ],
                'show' => [
                    'id' => $this->id,
                    'breed_id' => $this->breed_id,
                    'breed_title' => $this->breed_title,
                    'name' => $this->name,
                    'breed_type' => $this->breed_type,
                    'gender' => $this->gender,
                    'weight' => $this->weight,
                    'birth' => $this->birth,
                    'color' => $this->color,
                    'avatar' => $this->avatar,
                    'remark' => $this->remark,
                    'is_sterilization' => $this->is_sterilization,
                    'is_default' => $this->is_default,
                ],
                'default' => []
            },
            false => [
                'id' => $this->id,
                'breed_id' => $this->breed_id,
                'breed_title' => $this->breed_title,
                'name' => $this->name,
                'breed_type_conv' => strtoupper(PetCategoryEnum::from($this->breed_type)->name),
                'gender_conv' => GenderEnum::from($this->gender)->name('animal'),
                'weight' => $this->weight,
                'weight_conv' => PetWeightRangeEnum::from($this->weight)->name(),
                'age' => calculateAge($this->birth, 'Y-m'),
                'avatar' => $this->avatar,
            ]
        };
    }
}
