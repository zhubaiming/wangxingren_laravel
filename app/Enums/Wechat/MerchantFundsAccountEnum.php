<?php

namespace App\Enums\Wechat;

use App\Enums\CommonEnum;

enum MerchantFundsAccountEnum: int implements CommonEnum
{
    case UNSETTLED = 1;
    case AVAILABLE = 2;
    case UNAVAILABLE = 3;
    case OPERATION = 4;
    case BASIC = 5;
    case ECNY_BASIC = 6;


    public function name(string $type = null): string
    {
        return match ($this) {
            self::UNSETTLED => '未结算资金',
            self::AVAILABLE => '可用余额',
            self::UNAVAILABLE => '不可用余额',
            self::OPERATION => '运营账户',
            self::BASIC => '基本账户（含可用余额和不可用余额）',
            self::ECNY_BASIC => '数字人民币基本账户'
        };
    }
}
