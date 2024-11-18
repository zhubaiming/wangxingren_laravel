<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Services\Wechat\MiniAppServerSideService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(MiniAppServerSideService $service)
    {
        $this->service = $service;
    }

//    public function login(Request $request, MiniAppServerSideService $service)
//    {
//        try {
//            $validatedData = $request->validate([
//                'phone_code' => ['bail', 'required', 'alpha_num'],
//                'login_code' => ['bail', 'required', 'alpha_num']
//            ]);
//
//            $code_session = $service->code2session($validatedData['login_code']);
//            $phone_info = $service->getPhoneNumber($validatedData['phone_code'], $code_session['openid']);
//
//            $token = Auth::guard('wechat')->attempt(array_merge_recursive($code_session, $phone_info));
//        } catch (ValidationException $validationException) {
////            dd($validatedData->errors());
//            dd($validationException);
//        }
//
////        dd($token);
//
////        return $this->noContent();
//
//        return response(['status' => 0, 'message' => 'ok', 'data' => $token])->cookie('wxr_session_id', $token);
//    }

    public function silentLogin(Request $request)
    {
        return rescue(function () use ($request) {
            $validatedData = $request->validate([
                'code' => ['bail', 'required', 'string'],
                'device' => ['bail', 'required'],
                'app_base' => ['bail', 'required'],
            ]);

            $code_session = $this->service->code2session($validatedData['code']);

            list($token, $isRegister) = Auth::guard('wechat')->silentLogin(array_merge_recursive($code_session, $validatedData));

            return $this->success(['token' => $token, 'isRegister' => $isRegister]);
        }, function ($exception) {
            dd($exception);
        }, false);
    }


    public function registerLogin(Request $request)
    {
        return rescue(function () use ($request) {
            $validatedData = $request->validate([
                'code' => ['bail', 'required', 'alpha_num']
            ]);

            [$app_type, $appid, $wechat_openid] = Auth::guard('wechat')->decryptSilentToken();

            $phone_info = $this->service->getPhoneNumber($validatedData['code'], $wechat_openid);

            $token = Auth::guard('wechat')->attempt($phone_info);

            if (is_null($token)) {
                return response()->json([
                    'status' => '-1',
                    'message' => '注册失败，请重新进行微信登录后再进行注册'
                ], 500);
            }

//            return response()->json([
//                'status' => '0',
//                'message' => 'success',
//                'data' => ['token' => $token]
//            ]);
            return $this->success(['token' => $token]);
        }, function ($exception) {
            dd($exception);
        }, false);
    }
}
