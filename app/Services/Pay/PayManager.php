<?php

namespace App\Services\Pay;

class PayManager
{
    public function alipay()
    {

    }

    public function wechat()
    {
        return new Wechat();
    }
}