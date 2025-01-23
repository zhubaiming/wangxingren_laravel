<?php

namespace App\Enums\Wechat;

use App\Enums\CommonEnum;

enum MerchantChannelEnum: int implements CommonEnum
{
    case ORIGINAL = 1;
    case BALANCE = 2;
    case OTHER_BALANCE = 3;
    case OTHER_BANKCARD = 4;

    public function name(string $type = null): string
    {
        return match ($this) {
            self::ORIGINAL => '原路退款',
            self::BALANCE => '退回到余额',
            self::OTHER_BALANCE => '原账户异常退到其他余额账户',
            self::OTHER_BANKCARD => '原银行卡异常退到其他银行卡(发起异常退款成功后返回)'
        };
    }
}
