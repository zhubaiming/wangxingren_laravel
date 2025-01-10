<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Http\Controllers\Controller;
use App\Services\Wechat\MiniAppServerSideService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(MiniAppServerSideService $service)
    {
        $this->service = $service;
    }

    public function silentLogin(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $code_session = $this->service->code2session($validated['code']);

        [$token, $isRegister] = Auth::guard('wechat')->silentLogin(array_merge_recursive($code_session, $validated));

        return $this->success(['token' => $token, 'isRegister' => $isRegister]);
    }

    public function registerLogin(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        [$app_type, $appid, $wechat_openid] = Auth::guard('wechat')->decryptSilentToken();

        $phone_info = $this->service->getPhoneNumber($validated['code'], $wechat_openid);

        $token = Auth::guard('wechat')->attempt($phone_info);

        return $this->success(['token' => $token]);
    }
}
