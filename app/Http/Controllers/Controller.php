<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseCollection;
use App\Support\Traits\ApiResponse;

abstract class Controller
{
    //
//    use JsonResponseTrait;
    use ApiResponse;

    protected $service;

    protected int $page = 1;

    protected int $pageSize = 10;

    protected function returnIndex($payload, $resourceName, $format, $paginate = true): BaseCollection
    {
        return (new BaseCollection($payload))->additional(['resource' => "App\Http\Resources\\$resourceName", 'format' => $format, 'paginate' => $paginate]);
    }
}
