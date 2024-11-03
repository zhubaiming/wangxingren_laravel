<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Contracts\Auth\Factory as Auth;

class AuthenticateWechat
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        if (!$request->hasHeader('token')) {
//            return response()->json([
//                'code' => 208,
//                'message' => '请先进行登录'
//            ]);
//        }

        if (!$request->cookie($this->auth->guard('wechat')->getRecallerName())) {
            return response()->json([
                'code' => 208,
                'message' => '请先进行登录'
            ]);
        }

        if ($request->is('wechat/registerLogin')) {
            return $this->registerMid($request, $next);
        } else {
            return $this->defaultMid($request, $next);
        }
    }

    private function registerMid($request, $next)
    {
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
                'status' => '-1',
                'message' => '登录已过期，请重新登录'
            ], 401);
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
