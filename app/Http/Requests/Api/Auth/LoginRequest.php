<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\CommentsRequest;

class LoginRequest extends CommentsRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account' => ['required'],
            'password' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '用户名不能为空',
            'password.required' => '密码不能为空'
        ];
    }
}
