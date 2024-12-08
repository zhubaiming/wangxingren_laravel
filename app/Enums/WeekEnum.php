<?php

namespace App\Enums;

enum WeekEnum: int implements CommonEnum
{
    case Sun = 0;

    case Mon = 1;

    case Tues = 2;

    case Wed = 3;

    case Thur = 4;

    case Fri = 5;

    case Sat = 6;

    public function name(string $type = null)
    {
        return match ($this) {
            self::Sun => __('common.week.sunday'),
            self::Mon => __('common.week.monday'),
            self::Tues => __('common.week.tuesday'),
            self::Wed => __('common.week.wednesday'),
            self::Thur => __('common.week.thursday'),
            self::Fri => __('common.week.friday'),
            self::Sat => __('common.week.saturday')
        };
    }
}
