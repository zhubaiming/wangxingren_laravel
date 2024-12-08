<?php

namespace App\Support\Traits;

class Format
{
    public function data(mixed $data = null, string $message = '', int $code = 200, $error = null)
    {
        return tap($this, function () use ($data, $message, $code, $error) {

        });
    }

    protected function formatBusinessCode()
    {

    }
}