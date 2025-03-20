<?php

namespace App\Enums;

enum PetWeightRangeEnum: int implements CommonEnum
{
//    case FIVE = 5;
//    case EIGHT = 8;
//    case ELEVEN = 11;
//    case SIXTEEN = 16;
//    case TWENTY_ONE = 21;
//    case TWENTY_SIX = 26;
//    case THIRTY = 30;
//    case FORTY = 40;
//    case SIXTY = 60;
//    case EIGHTY = 80;
//    case HUNDRED = 100;

    case FIVE = 500;
    case SEVEN_POINT_FIVE = 750;
    case TEN = 1000;
    case FIFTEEN = 1500;
    case TWENTY = 2000;
    case TWENTY_FIVE = 2500;
    case THIRTY = 3000;
    case FORTY = 4000;
    case FIFTY = 5000;
    case SIXTY = 6000;
    case EIGHTY = 8000;
    case HUNDRED = 10000;

    /*
     * 0-5
     * 5-7.5
     * 7.5-10
     * 10-15
     * 15-20
     * 20-25
     * 25-30
     * 30-40
     * 40-50
     * 50-60
     * 60-80
     * 80-100
     */


    public function name(string $type = null)
    {
        return match ($this) {
//            self::FIVE => '0-5公斤',
//            self::EIGHT => '5-8公斤',
//            self::ELEVEN => '8-11公斤',
//            self::SIXTEEN => '11-16公斤',
//            self::TWENTY_ONE => '16-21公斤',
//            self::TWENTY_SIX => '21-26公斤',
//            self::THIRTY => '26-30公斤',
//            self::FORTY => '30-40公斤',
//            self::SIXTY => '40-60公斤',
//            self::EIGHTY => '60-80公斤',
//            self::HUNDRED => '80-100公斤'
            self::FIVE => '0-5公斤',
            self::SEVEN_POINT_FIVE => '5-7.5公斤',
            self::TEN => '7.5-10公斤',
            self::FIFTEEN => '10-15公斤',
            self::TWENTY => '15-20公斤',
            self::TWENTY_FIVE => '20-25公斤',
            self::THIRTY => '25-30公斤',
            self::FORTY => '30-40公斤',
            self::FIFTY => '40-50公斤',
            self::SIXTY => '50-60公斤',
            self::EIGHTY => '60-80公斤',
            self::HUNDRED => '80-100公斤'
        };
    }
}