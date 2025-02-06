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
                'created_at' => $this->created_at
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
