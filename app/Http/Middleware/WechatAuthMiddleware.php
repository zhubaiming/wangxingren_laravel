<?php

namespace App\Http\Middleware;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WechatAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('wechat')->check()) {
            throw new BusinessException([401, ResponseEnum::get($request->input('http_error'))[1]]);
        }

//        $response = $next($request)
//            ->header('Access-Control-Allow-Origin', 'http://192.168.31.4:3001')  // 允许跨域的源
//            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')  // 允许的请求方法
//            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization')  // 允许的请求头
//            ->header('Access-Control-Allow-Credentials', 'true');  // 是否允许携带凭证（如 cookies）

        $response = $next($request)
            ->header('Access-Control-Allow-Origin', '*');  // 允许跨域的源

        if ($request->bearerToken() !== Auth::guard('wechat')->getToken()) {
            $response->header('Refresh', Auth::guard('wechat')->getToken());
        }

        return $response;
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
