<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class ClientUserAddressResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'province' => $this->province,
                    'city' => $this->city,
                    'district' => $this->district,
                    'street' => $this->street,
                    'address' => $this->address,
                    'full_address' => $this->full_address,
                    'person_name' => $this->person_name,
                    'person_phone_prefix' => $this->person_phone_prefix,
                    'person_phone_number' => $this->person_phone_number,
                    'is_default' => $this->is_default
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
                'province' => $this->province,
                'city' => $this->city,
                'district' => $this->district,
                'street' => $this->street,
                'address' => $this->address,
                'full_address' => $this->full_address,
                'person_name' => $this->person_name,
                'person_phone_prefix' => $this->person_phone_prefix,
                'person_phone_number' => $this->person_phone_number,
                'is_default' => $this->is_default
            ]
        };
    }
}
