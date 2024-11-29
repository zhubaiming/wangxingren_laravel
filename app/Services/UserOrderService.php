<?php

namespace App\Services;

class UserOrderService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\ClientUserOrder');

        $this->setTable('sys_goods_spu');

        $this->events = app('events');
    }
}