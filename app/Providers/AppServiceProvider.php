<?php

namespace App\Providers;

use App\Services\Guard\AdminGuard;
use App\Services\Wechat\WechatAppUserGuard;
use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 添加自定义守卫
        Auth::extend('wechat', function (Application $app, string $name, array $config) {
            // 返回 Illuminate\Contracts\Auth\Guard 的实例

            return new WechatAppUserGuard($app, Auth::createUserProvider($config['provider']));
        });
        Auth::extend('admin', function ($app, $name, $config) {

//            dd(Auth::createUserProvider($config['provider']));

            return new AdminGuard($app, Auth::createUserProvider($config['provider']));

//            return new RequestGuard(
//                new AdminGuard(),
//                request(),
//                Auth::createUserProvider($config['provider'])
//            );


//             new Illuminate\Auth\RequestGuard(
            //    new Guard($auth, config('sanctum.expiration), $config['provider']),
            //    request(),
            //    $auth->createUserProvider($config['provider'] ?? null)
//            );
        });


        // Eloquent
        // 在非生产环境中禁用懒加载
//        Model::preventLazyLoading(!$this->app->isProduction());
        // Http客户端
        // 全局选项
        Http::globalOptions([
            'connect_timeout' => 20,
//            'debug' => !$this->app->isProduction(),
            'debug' => $this->app->isLocal(),
            'timeout' => 10
        ]);
        // 大量赋值异常【默认大量赋值时，不在 $fillable 数组红的属性会被默默丢弃，这是预期的生产行为，但在本地开发中，可以尝试在填充一个不可填充的属性时抛出异常】
        Model::preventSilentlyDiscardingAttributes($this->app->isLocal());
        // DB
        // 监听查询事件
        DB::listen(function (QueryExecuted $query) {
            Log::channel('sql_daily')->info(Str::replaceArray('?', $query->bindings, $query->sql));
        });
        // 去除 API资源化 的数据包装(即: 响应资源转换为 JSON 时，最外层资源包装的 data)
        JsonResource::withoutWrapping();
        // 启用全局返回 JSON数据 时，不再转义中文字符
        // 全局设置 JSON_UNESCAPED_UNICODE 选项
//        response()->macro('json', function ($data = [], $status = 200, array $headers = [], $options = 0) {
//            return response()->json($data, $status, $headers, JSON_UNESCAPED_UNICODE);
//        });
    }
}
