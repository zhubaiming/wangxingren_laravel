<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class GoodsSkuResource extends CommentsResource
{
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'price_conv' => $this->price_conv,
        ];
    }
}
