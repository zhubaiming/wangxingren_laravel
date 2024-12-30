<?php

namespace App\Enums;

enum GenderEnum: int implements CommonEnum
{
    case unknown = 0;
    case male = 1;
    case female = 2;

    public function name(string $type = null)
    {
        return match ($this) {
            self::unknown => __('common.unknown'),
            self::male => __('enums.gender.' . $type . '.male'),
            self::female => __('enums.gender.' . $type . '.female')
        };
    }
}
