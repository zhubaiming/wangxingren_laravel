<?php

namespace App\Providers;

use App\Services\Wechat\WechatAppUserGuard;
use App\Services\WechatAppUserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
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
        //
        Auth::provider('wechat', function (Application $app, array $config) {
            // 返回 Illuminate\Contracts\Auth\UserProvider 的实例

            return new WechatAppUserService($app->make('mongo.connection'));
        });
        // Eloquent
        // 在非生产环境中禁用懒加载
        Model::preventLazyLoading(!$this->app->isProduction());
        // Http客户端
        // 全局选项
        Http::globalOptions([
            'connect_timeout' => 20,
            'debug' => !$this->app->isProduction(),
            'timeout' => 10
        ]);
        // 大量赋值异常【默认大量赋值时，不在 $fillable 数组红的属性会被默默丢弃，这是预期的生产行为，但在本地开发中，可以尝试在填充一个不可填充的属性时抛出异常】
        Model::preventSilentlyDiscardingAttributes($this->app->isLocal());
        // DB
        // 监听查询事件
        DB::listen(function (QueryExecuted $query) {
            Log::channel('sql_daily')->info(Str::replaceArray('?', $query->bindings, $query->sql));
        });
    }
}
