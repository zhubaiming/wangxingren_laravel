<?php

namespace App\Models;

class ProductServiceTime extends CommentsModel
{
    protected $table = 'sys_product_service_time';

    // 属性类型转换
    protected function casts()
    {
        return [
//            'date' => 'date:Y-m-d',
//            'start_time' => 'datetime:H:i:S',
//            'end_time' => 'datetime:H:i:S',
            'enable' => 'boolean',
        ];
    }
}