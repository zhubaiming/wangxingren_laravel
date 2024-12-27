<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function registered(Request $request): \Illuminate\Http\JsonResponse
    {
        $validate = $request->validate([
            'account' => ['bail', 'required', 'alpha_num'],
            'role_id' => ['bail', 'required', 'integer'],
            'name' => ['bail', 'required', 'string'],
            'gender' => ['bail', 'required', 'integer'],
            'phone_number' => ['bail', 'required', 'string'],
            'password' => ['bail', 'exclude'],
            'avatar' => ['bail', 'sometimes', 'url'],
        ]);

        if (0 !== User::where('account', $validate['account'])->orWhere('phone_number', $validate['phone_number'])->count('id')) {
            throw new BusinessException(ResponseEnum::USER_ACCOUNT_REGISTERED);
        }

        User::create(array_merge($validate, [
            'uid' => strval(Str::ulid()),
            'password' => Hash::make('Dcba@1234'),
            'status' => true,
            'is_default_passwd' => true,
            'updated_by' => $request->input('user')
        ]));

        return $this->success();
    }


    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validate = $request->validate([
            'account' => ['required', 'alpha_num'],
            'password' => ['required'],
            'captcha' => ['required', 'alpha_num']
        ]);

        // todo: 校验验证码
//        User::where('account', '15840132829')->update([
////            'uid' => strval(Str::ulid()),
//            'password' => Hash::make('Dcba@1234')
//        ]);

        unset($validate['captcha']);

        $auth = Auth::guard('admin');

        if ($auth->validate($validate)) {
            if (!$auth->getLastAttempted()->status) {
                throw new BusinessException(ResponseEnum::USER_SERVICE_LOGIN_STATUS_ERROR);
            }

            $token = $auth->login($auth->getLastAttempted());

            return $this->success(compact('token'));
        } else {
            throw new BusinessException(ResponseEnum::USER_SERVICE_LOGIN_ERROR);
        }
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        Auth::guard('admin')->logout();

        return $this->success();
    }

    public function authenticate(): \Illuminate\Http\JsonResponse
    {
        if (Auth::guard('admin')->check()) {
            return $this->success();
        } else {
            throw new BusinessException(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED);
        }
    }
}
