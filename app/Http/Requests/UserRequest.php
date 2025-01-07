<?php

namespace App\Http\Requests;


class UserRequest extends CommentsRequest
{
    public function rules(): array
    {
        return match ($this->method()) {
            'GET' => [],
            'POST' => [
                'account' => ['required'],
                'password' => ['required'],
                'captcha' => ['required'],
            ],
            'PUT' => [],
            'PATCH' => [],
            'DELETE' => [],
            default => []
        };
    }
}
