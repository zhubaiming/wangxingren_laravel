<?php

namespace App\Enums;

enum UserStateEnum: int implements CommonEnum
{
    const INVALID = -1;
    const NORMAL = 0;
    const FREEZE = 1;

    public function name(string $type = null)
    {
        // TODO: Implement name() method.
    }

    public static function getStatusName($status)
    {
        return match ($status) {
            self::INVALID => __('enums.user_status.invalid'),
            self::NORMAL => __('enums.user_status.normal'),
            self::FREEZE => __('enums.user_status.freeze'),
            default => __('common.unknown')
        };
    }
}
