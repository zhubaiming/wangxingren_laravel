<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class UserCouponResource extends CommentsResource
{
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'range' => $this->range,
            'description' => $this->description,
            'min_price' => $this->min_price,
            'amount' => $this->amount,
            'expiration_timestamp' => strtotime($this->expiration_time)
        ];
    }
}
