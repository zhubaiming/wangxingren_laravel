<?php

namespace App\Enums;

enum PetCategoryEnum: int implements CommonEnum
{
    case unknown = 0;
    case cat = 1;
    case dog = 2;

    public function name(string $type = null)
    {
        return match ($this) {
            self::unknown => '未知',
            self::cat => '猫',
            self::dog => '狗'
//            self::unknown => __('common.unknown'),
//            self::cat => __('enums.animal.type.cat'),
//            self::dog => __('enums.animal.type.dog')
        };
    }
}
