<?php

namespace App\Enums;

enum PetWeightRangeEnum: int implements CommonEnum
{
    case FIVE = 5;
    case EIGHT = 8;
    case ELEVEN = 11;
    case SIXTEEN = 16;
    case TWENTY_ONE = 21;
    case TWENTY_SIX = 26;
    case THIRTY = 30;
    case FORTY = 40;
    case SIXTY = 60;
    case EIGHTY = 80;
    case HUNDRED = 100;


    public function name(string $type = null)
    {
        return match ($this) {
            self::FIVE => '0-5公斤',
            self::EIGHT => '5-8公斤',
            self::ELEVEN => '8-11公斤',
            self::SIXTEEN => '11-16公斤',
            self::TWENTY_ONE => '16-21公斤',
            self::TWENTY_SIX => '21-26公斤',
            self::THIRTY => '26-30公斤',
            self::FORTY => '30-40公斤',
            self::SIXTY => '40-60公斤',
            self::EIGHTY => '60-80公斤',
            self::HUNDRED => '80-100公斤'
        };
    }
}