<?php

namespace App\Services;

class GoodsSkuService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\SysGoodsSku');

        $this->setTable('sys_goods_sku');

        $this->events = app('events');
    }
}