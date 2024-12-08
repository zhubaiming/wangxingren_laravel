<?php

namespace App\Enums;

enum GenderEnum: int implements CommonEnum
{
    case unknown = 0;
    case man = 1;
    case woman = 2;

    public function name(string $type = null)
    {
        return match ($this) {
            self::unknown => __('common.unknown'),
            self::man => __('enums.gender.' . $type . '.man'),
            self::woman => __('enums.gender.' . $type . '.woman')
        };
    }
}
