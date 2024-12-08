<?php

namespace App\Services;

class ProductServiceTimeService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\ProductServiceTime');
    }
}