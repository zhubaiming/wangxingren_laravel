<?php

namespace App\Models;

class ProductSpuDetail extends CommentsModel
{
    protected $table = 'sys_product_spu_detail';

    protected function casts()
    {
        return [
            'images' => 'array',
        ];
    }
}
