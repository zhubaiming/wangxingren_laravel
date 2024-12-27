<?php

namespace App\Http\Middleware;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Support\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/*')) {
            if ($request->is('*/login')) {
                if (!is_null($request->bearerToken())) {
                    if (!Auth::guard('admin')->check()) {
                        throw new BusinessException(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED);
                    } else {
                        $token = Redis::hget('user_login', Auth::guard('admin')->id());
                    }
                    return $this->success(compact('token'));
                } else {
                    return $next($request);
                }
            } else {
                if (is_null($request->bearerToken())) {
                    throw new BusinessException(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED);
                }

                if (!Auth::guard('admin')->check()) {
                    throw new BusinessException(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED);
                }
            }
        }

        $request->merge(['user' => implode('-', array_intersect_key(Auth::guard('admin')->user()->getAttributes(), array_flip(['name', 'account'])))]);

        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://192.168.31.4:3001')  // 允许跨域的源
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')  // 允许的请求方法
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization')  // 允许的请求头
            ->header('Access-Control-Allow-Credentials', 'true');  // 是否允许携带凭证（如 cookies）
    }
}
