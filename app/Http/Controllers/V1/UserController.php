<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if ($request->post('username') === 'wangxingren' && $request->post('password') === 'wangxingren') {
            return $this->message('success');
        }

//        return $this->message('用户名或密码错误');
        return $this->failed('用户名或密码错误');
//        return $this->internalError('用户名或密码错误');
    }
}
