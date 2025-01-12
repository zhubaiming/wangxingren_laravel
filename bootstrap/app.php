<?php

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Middleware as CustomMiddleware;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware([
                'auth.wechat'
            ])
                ->prefix('wechat')
                ->name('wechat.')
                ->group(base_path('routes/wechat.php'));

//            Route::middleware('api')
//                ->prefix('wechat_notify')
//                ->name('wechat_notify.')
//                ->group(base_path('routes/wechat_notify.php'));

            Route::prefix('wechat_notify')
                ->name('wechat_notify.')
                ->group(base_path('routes/wechat_notify.php'));

            Route::prefix('test')
                ->name('test.')
                ->group(base_path('routes/test.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 手动管理 Laravel 默认的中间件组
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        $middleware->group('api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // 'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            CustomMiddleware\ApiAuthMiddleware::class,
        ]);
        // 中间件别名
        $middleware->alias([
            'auth.wechat' => CustomMiddleware\WechatAuthMiddleware::class
        ]);
        /*
         * 配置可信代理
         * at: ip字符串数组
         * headers: Request::头
         *
         * 信任所有代理 at: '*'
         */
        $middleware->trustProxies(at: [
            '192.168.65.1',
            '192.22.0.0',
            '192.22.0.1',
            '192.22.0.2',
            '172.22.0.3',
            '172.22.0.4',
            '172.22.0.5',
        ]);
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
//        $exceptions->render(function (Exception $e) {
//            $message = $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine();
//            $state = -2;
//            $code = 500;
//        });
        $exceptions->render(function (Exception $e) {
//            dd($e);
            // 生产环境直接返回 500
            if (!config('app.debug')) {
                // ResponseEnum::SYSTEM_ERROR
            }

            switch (true) {
                case $e instanceof MethodNotAllowedHttpException: // 请求类型错误异常抛出
                    throw new BusinessException(ResponseEnum::CLIENT_METHOD_HTTP_TYPE_ERROR);
                case $e instanceof ValidationException: // 参数校验错误异常抛出
                    throw new BusinessException(ResponseEnum::CLIENT_PARAMETER_ERROR, config('app.debug') ? $e->getMessage() : '');
                case $e instanceof QueryException: // Sql错误异常抛出
                case $e instanceof MassAssignmentException: // Sql错误异常抛出
                case $e instanceof RelationNotFoundException: // 关联关系异常
                    throw new BusinessException(ResponseEnum::HTTP_ERROR, config('app.debug') ? $e->getMessage() : '');
                case $e instanceof NotFoundHttpException:
                    throw new BusinessException(ResponseEnum::CLIENT_DELETED_ERROR, config('app.debug') ? $e->getMessage() : '');
                case $e instanceof BusinessException: // 自定义错误异常抛出
                    return response()->json([
                        'status' => 'fail',
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'data' => null,
                        'error' => null
                    ]);
            }

            dd($e);

            // 参数校验错误异常抛出
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                // ResponseEnum::CLIENT_PARAMETER_ERROR
            }

            // 路由不存在异常抛出
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                // ResponseEnum::CLIENT_NOT_FOUND_ERROR
            }
        });

//        dd(request()->expectsJson());

        if (\request()->expectsJson()) {
            $exceptions->render(function (Exception $e) {
                // 生产环境直接返回 500
                if (!config('app.debug')) {
                    // ResponseEnum::SYSTEM_ERROR
                }

//                switch (true){
//                    case $e instanceof
//                }

                // 请求类型错误异常抛出
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    throw
                        // ResponseEnum::CLIENT_METHOD_HTTP_TYPE_ERROR
                    dd(456);
                }

                // 参数校验错误异常抛出
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    // ResponseEnum::CLIENT_PARAMETER_ERROR
                }

                // 路由不存在异常抛出
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    // ResponseEnum::CLIENT_NOT_FOUND_ERROR
                }

                // 自定义错误异常抛出
                if ($e instanceof \App\Exceptions\BusinessException) {
                    return response()->json();
                }
            });
        }
    })->create();
