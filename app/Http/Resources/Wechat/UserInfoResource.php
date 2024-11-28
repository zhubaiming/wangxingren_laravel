<?php

namespace App\Http\Resources\Wechat;

use App\Enums\GenderEnum;
use App\Http\Resources\CommentsResource;

class UserInfoResource extends CommentsResource
{

    protected function resourceData(): array
    {
        return [
            'nick_name' => $this->nick_name,
            'avatar' => $this->avatar,
            'gender' => GenderEnum::from($this->gender)->name('people'),
            'level' => 5,
            'integral' => 65535,
            'created_at' => date('Y年m月d日', strtotime($this->created_at)),
        ];
    }
}
