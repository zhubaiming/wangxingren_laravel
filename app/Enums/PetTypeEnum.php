<?php

namespace App\Enums;

enum PetTypeEnum: int implements CommonEnum
{
    case unknown = 0;
    case dog = 1;
    case cat = 2;

    public function name(string $type = null)
    {
        return match ($this) {
            self::unknown => __('common.unknown'),
            self::dog => __('enums.animal.type.dog'),
            self::cat => __('enums.animal.type.cat')
        };
    }
}
