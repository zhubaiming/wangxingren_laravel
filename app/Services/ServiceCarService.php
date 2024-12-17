<?php

namespace App\Services;

class ServiceCarService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\ServiceCar');
    }
}