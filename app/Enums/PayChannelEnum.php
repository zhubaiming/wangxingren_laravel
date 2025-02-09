<?php

namespace App\Enums;

enum PayChannelEnum: int implements CommonEnum
{
    case unknown = 0;
    case wechat = 1;
    case alipay = 2;
    case card = 3;
    case cash = 4;
    case wxtransfer = 5;
    case alitransfer = 6;

    public function name(string $type = null)
    {
        return match ($this) {
            self::unknown => __('common.unknown'),
            self::wechat => '微信支付',
            self::alipay => '支付宝支付',
            self::card => '会员卡消费',
            self::cash => '现金',
            self::wxtransfer => '微信转账',
            self::alitransfer => '支付宝转账',
        };
    }

    public static function getOffLineChannels(): array
    {
        return [
            self::card->value,
            self::cash->value,
            self::wxtransfer->value,
            self::alitransfer->value,
        ];
    }
}
