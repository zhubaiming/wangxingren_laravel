<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

//use Illuminate\Contracts\Auth\Factory as Auth;

class WechatAuthMiddleware
{
//    protected $auth;
//
//    public function __construct(Auth $auth)
//    {
//        $this->auth = $auth;
//    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (is_null($request->bearerToken())) {
            dd(1);
        }

        $user = Auth::guard('wechat')->user();

        if (Auth::guard('wechat')->guest()) {

        }

//        if ($request->is('api/*')) {
//            if ($request->is('*/login')) {
//                if (!is_null($request->bearerToken())) {
//                    if (!\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
//                        throw new BusinessException(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED);
//                    } else {
//                        $token = Redis::hget('user_login', Auth::guard('admin')->id());
//                    }
//                    return $this->success(compact('token'));
//                } else {
//                    return $next($request);
//                }
//            } else {
//                if (is_null($request->bearerToken())) {
//                    throw new BusinessException(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED);
//                }
//
//                if (!Auth::guard('admin')->check()) {
//                    throw new BusinessException(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED);
//                }
//            }
//        }
//

        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://192.168.31.4:3001')  // 允许跨域的源
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')  // 允许的请求方法
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization')  // 允许的请求头
            ->header('Access-Control-Allow-Credentials', 'true');  // 是否允许携带凭证（如 cookies）

//        if (!$request->cookie($this->auth->guard('wechat')->getRecallerName())) {
//            return response()->json([
//                'code' => 208,
//                'message' => '请先进行登录'
//            ]);
//        }
//
//        if ($request->is('wechat/registerLogin')) {
//            return $this->registerMid($request, $next);
//        } else {
//            return $this->defaultMid($request, $next);
//        }
    }

    private function registerMid($request, $next)
    {
        if (!$request->hasHeader('appId') || $request->header('appId') !== config('wechat.miniprogram.app_id')) {
            dd('不让请求');
        }


        if (!$this->auth->guard('wechat')->checkSilentUser()) {
            return response()->json([
                'status' => '-1',
                'message' => '请先进行【微信】登录'
            ], 401);
        }

        if (3 != count($this->auth->guard('wechat')->decryptSilentToken())) {
            return response()->json([
                'status' => '-1',
                'message' => '登录信息有误'
            ], 401);
        }

        return $next($request);
    }

    private function defaultMid($request, $next)
    {
        if (is_null($this->auth->guard('wechat')->user())) {
            return response()->json([
                'code' => 208,
                'message' => '登录已过期，请重新登录'
            ], 200);
        }

        if (!$this->auth->guard('wechat')->refreshTokenExpiredTime()) {
            return response()->json([
                'status' => '-1',
                'message' => '登录已失效，请重新登录'
            ], 401);
        }

        return $next($request);
    }
}
