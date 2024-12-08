<?php

namespace App\Exceptions;

use App\Support\Traits\JsonResponseTrait;
use Exception;

class WechatApiException extends Exception
{
    use JsonResponseTrait;

    private $error_code;

    public function __construct($error_code, $message = "", $code = 0, ?\Throwable $previous = null)
    {
        $this->error_code = $error_code;

        parent::__construct($message, $code, $previous);
    }

    public function report()
    {
        if (app()->isProduction()) {
            return false;
        }

        return true;
    }

    public function render()
    {
        return $this->noData($this->error_code, $this->getMessage());
    }
}
