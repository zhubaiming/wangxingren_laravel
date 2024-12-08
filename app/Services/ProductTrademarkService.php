<?php

namespace App\Services;

class ProductTrademarkService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\ProductTrademark');
    }
}