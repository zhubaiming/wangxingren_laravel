<?php

namespace App\Models;

class ServiceCar extends CommentsModel
{
    protected $table = 'sys_service_car';

    protected function casts()
    {
        return [
            'status' => 'boolean',
        ];
    }
}
