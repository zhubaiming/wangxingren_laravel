<?php

namespace App\Enums\Wechat;

use App\Enums\CommonEnum;

enum MerchantStatusEnum: int implements CommonEnum
{
    case SUCCESS = 1;
    case CLOSED = 2;
    case PROCESSING = 3;
    case ABNORMAL = 4;

    public function name(string $type = null): string
    {
        return match ($this) {
            self::SUCCESS => '退款成功',
            self::CLOSED => '退款关闭',
            self::PROCESSING => '退款处理中',
            self::ABNORMAL => '退款异常' // 退款到银行发现用户的卡作废或者冻结了，导致原路退款银行卡失败，可前往商户平台-交易中心，手动处理此笔退款，可参考： 退款异常的处理，或者通过发起异常退款接口进行处理。注：状态流转说明请参考状态流转图
        };
    }
}
