<?php

namespace App\Http\Controllers;

use App\Support\Traits\JsonResponseTrait;

abstract class Controller
{
    //
    use JsonResponseTrait;

    protected $service;
}
