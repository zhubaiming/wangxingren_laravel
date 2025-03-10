<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class ClientUserResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $format = $this->additional['format'] ?? 'default';

        return match ($format) {
            'silentLogin' => [
                'nick_name' => $this->nick_name,
                'avatar' => $this->avatar,
                'created_at' => $this->created_at,
                'default_pet' => count($this->pets) > 0 ? (new ClientUserPetResource($this->pets[0]))->additional(['paginate' => false]) : null,
                'default_address' => count($this->addresses) > 0 ? (new ClientUserAddressResource($this->addresses[0]))->additional(['paginate' => false]) : null,
            ],
            'registerLogin' => [
                'nick_name' => $this->nick_name,
                'avatar' => $this->avatar,
                'created_at' => $this->created_at
            ],
            'default' => [

            ]
        };
    }
}
