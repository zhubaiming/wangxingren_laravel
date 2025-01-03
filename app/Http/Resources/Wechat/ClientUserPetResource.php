<?php

namespace App\Http\Resources\Wechat;

use App\Enums\PetCategoryEnum;
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
                    'gender_conv' => $this->gender_conv,
                    'weight' => $this->weight,
                    'birth' => $this->birth,
                    'age' => $this->age,
                    'color' => $this->color,
                    'avatar' => $this->avatar,
                    'remark' => $this->remark,
                    'is_sterilization' => $this->is_sterilization,
                    'is_default' => $this->is_default,
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'permissions' => $this->permissions->pluck('id'),
                    'menus' => $this->menus->pluck('id')
                ],
                'default' => []
            },
            false => [
                'id' => $this->id,
                'breed_id' => $this->breed_id,
                'breed_title' => $this->breed_title,
                'name' => $this->name,
                'breed_type' => $this->breed_type,
                'breed_type_conv' => $this->breed_type_conv,
                'gender' => $this->gender,
                'gender_conv' => $this->gender_conv,
                'weight' => $this->weight,
                'birth' => $this->birth,
                'age' => $this->age,
                'color' => $this->color,
                'avatar' => $this->avatar,
                'remark' => $this->remark,
                'is_sterilization' => $this->is_sterilization,
                'is_default' => $this->is_default,
            ]
        };
    }
}
