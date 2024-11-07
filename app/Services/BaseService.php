<?php

namespace App\Services;

class BaseService
{
    protected $model;

    protected $events;

    protected function setModel($model_name)
    {
        $class = '\\' . ltrim($model_name, '\\');

        $this->model = new $class;
    }
}