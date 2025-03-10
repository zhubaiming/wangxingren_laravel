<?php

use Illuminate\Support\Facades\Facade;

class Pay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'pay';
    }
}