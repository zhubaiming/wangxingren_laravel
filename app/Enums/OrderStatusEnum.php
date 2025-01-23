<?php

namespace App\Enums;

enum OrderStatusEnum: int implements CommonEnum
{
    case cancel = 0;
    case paying = 1;
    case finishing = 2;
    case finished = 3;
    case refund = 4;
    case refunding = 5;
    case refunded = 6;

    public function name(string $type = null): string
    {
        return match ($this) {
//            self::wait => __('common.unknown'),
            self::cancel => '已取消',
            self::paying => '待付款',
            self::finishing => '待服务',
            self::finished => '已完成',
            self::refund => '已申请退款',
            self::refunding => '退款中',
            self::refunded => '退款完成'
        };
    }

    public static function getRefundStatuses(): array
    {
        return [
            self::refund->value,
            self::refunding->value,
            self::refunded->value,
        ];
    }

    public static function getFinishStatuses(): array
    {
        return [
            self::finishing->value,
            self::finished->value
        ];
    }
}
