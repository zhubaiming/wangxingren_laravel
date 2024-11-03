<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Middleware as CustomMiddleware;

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

            Route::middleware('api')
                ->prefix('wechat_notify')
                ->name('wechat_notify.')
                ->group(base_path('routes/wechat_notify.php'));

            Route::prefix('test')
                ->name('test.')
                ->group(base_path('routes/test.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        // 中间件别名
        $middleware->alias([
            'auth.wechat' => CustomMiddleware\AuthenticateWechat::class
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
    })->create();
