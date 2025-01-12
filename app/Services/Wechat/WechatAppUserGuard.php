<?php

namespace App\Services\Wechat;

use App\Models\ClientUserLoginInfo;
use App\Support\Traits\ApiToken;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;

/**
 * 自定义守卫
 *
 * 在 AppServiceProvider 中使用 extend 定义
 *
 * 传递给 extend 方法的回调函数应该返回 Illuminate\Contracts\Auth\Guard
 */
class WechatAppUserGuard implements StatefulGuard
{
    use GuardHelpers, ApiToken;

    public readonly string $name;

    public readonly string $redis_connection;

    private $app;

    protected $lastAttempted;

//    private $app;

    private $redis;

    private $request;

    private $uuid;

    private $events;

    private $token;

    private $login_info;

//    protected $lastAttempted;

    /*
     * Illuminate\Auth\GuardHelpers
     *
     * 定义的变量
     * protected $user;
     * protected $provider;
     *
     * 实现的方法
     * authenticate - 确定当前用户是否已通过身份验证，如果没有，则抛出异常(Illuminate\Auth\AuthenticationException)
     * hasUser - 确定护卫是否拥有用户实例
     * check - 确定当前用户是否已通过身份验证
     * guest - 确定当前用户是否为访客
     * id - 获取当前已通过身份验证的用户的 ID
     * setUser - 设置当前用户
     * forgetUser - 忘记当前用户
     * getProvider - 获取卫兵使用的用户提供程序
     * setProvider - 设置卫兵使用的用户提供程序
     */

    public function __construct($app, UserProvider $provider)
    {
        $this->name = 'user:uid:';

        $this->redis_connection = 'wechat_user';

        $this->app = $app;

        $this->redis = $app['redis']->connection('wechat_user');

        $this->request = $app['request'];

        $this->provider = $provider;
    }

    /**
     * 使用给定的用户进行登录
     *
     */
//    public function login($user)
    public function login(Authenticatable $user, $remember = false): string
    {
        $token = $this->updateToken($user->getAuthIdentifier());

        $this->setUser($user);

        return $token;
    }

    /**
     * 根据当前需要登录的用户生成单一设备登录对应关系，同时存储用户信息，并生成登录用户的 token
     *
     */
    protected function updateToken($primaryKey): string
    {
        $token = $this->generateJsonWebToken([
            'sub' => $primaryKey
        ]);

        Redis::connection($this->redis_connection)->hset('user_login', $primaryKey, $token);

        return $token;
    }

    public function setUser(Authenticatable $user): WechatAppUserGuard|static
    {
        $this->user = $user;

        if (0 === Redis::connection($this->redis_connection)->exists($this->name . $user->getAuthIdentifier())) {
            Redis::connection($this->redis_connection)->hmset($this->name . $user->getAuthIdentifier(), [
                'laravel_ORM' => serialize($user),
                'json' => $user->toJson(JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ]);
        } else {
            Redis::connection($this->redis_connection)->hset($this->name . $user->getAuthIdentifier(), [
                'laravel_ORM' => serialize($user)
            ]);
        }

        return $this;
    }

//    public function setUser(Authenticatable|array $user): static
//    {
//        $this->user = $user;
//
//        return $this;
//    }


    /**
     * 静默登录
     *
     * @param array $credentials
     * @return mixed
     */
    public function silentLogin(array $credentials = []): mixed
    {
        /**
         * $credentials =
         * [
         *   'code'=>'',
         *   'device'=> [
         *     'benchmarkLevel'=> -1,
         *     'brand'=> 'devtools',
         *     'memorySize'=> 2048,
         *     'model'=> 'iPhone 6/7/8',
         *     'system'=> 'iOS 10.0.1',
         *     'platform'=> 'devtools'
         *   ],
         *   'app_base'=> [
         *     'version'=> '8.0.5',
         *     'language'=> 'zh_CN',
         *     'SDKVersion'=> '3.5.8',
         *     'enableDebug'=> true,
         *     'fontSizeScaleFactor'=> 0.61,
         *     'fontSizeSetting'=> 16,
         *     'mode'=> 'default',
         *     'host'=> [
         *       'env'=> 'WeChat'
         *     ]
         *   ],
         *   'session_key' => 'YBmLfJzSL4jlGGQf26tV+A==',
         *   'unionid' => null,
         *   'errmsg' => null,
         *   'openid' => 'oaQAW7UWF6z-jH6YljVJi4uvtdI4',
         *   'errcode' => null
         * ]
         */

        $isRegister = false;

        // 查询当前静默信息是否存在
        $this->login_info = $this->retrieveSilentLoginInfoByCredentials([
            'wechat_openid' => $credentials['openid']
        ]);

        $this->setSilentLoginUserToken($credentials['openid']);

        if (is_null($this->login_info)) {
            $this->login_info = $this->createSilentLoginInfoByCredentials($credentials);

            $this->createLoginDeviceByInfo($credentials);
        } else {
            $user_info = $this->login_info->user();
            if (0 != $user_info->count() && !is_null($purePhoneNumber = $user_info->sole(['phone_number'])->phone_number)) {
                $this->token = $this->attempt(['purePhoneNumber' => $purePhoneNumber], $this->token);
                $isRegister = true;
            }
        }

        return [$this->token, $isRegister];
    }

    /**
     * 根据用户的 openid 生成静默登录的临时 token，并存储到 redis 中
     *
     * @param $openid
     * @return void
     */
    private function setSilentLoginUserToken($openid): void
    {
        $this->token = Crypt::encryptString('wechat_miniprogram.' . config('wechat.miniprogram.app_id') . '.' . $openid);

        $this->redis->sadd('silent_login_user_token_set', $this->token);
    }

    /**
     * 根据传入条件检验静默登录信息
     *
     * @param $credentials
     * @return null
     */
    private function retrieveSilentLoginInfoByCredentials($credentials)
    {
        $credentials = array_merge_recursive(
            $credentials,
            ['app_type' => 'wechat_miniprogram', 'appid' => config('wechat.miniprogram.app_id')]
        );

        if (empty($credentials)) {
            return null;
        }

        $query = new ClientUserLoginInfo();

        foreach ($credentials as $key => $value) {
            if (is_array($value) || $value instanceof Arrayable) {
                $query = $query->whereIn($key, $value);
            } elseif ($value instanceof \Closure) {
                $query = $value($query);
            } else {
                $query = $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * 创建静默登录用户
     *
     * @param $credentials
     * @return mixed
     */
    private function createSilentLoginInfoByCredentials($credentials): mixed
    {
        $credentials = array_merge_recursive(
            $credentials,
            ['app_type' => 'wechat_miniprogram', 'appid' => config('wechat.miniprogram.app_id')]
        );

        return ClientUserLoginInfo::create([
            'app_type' => $credentials['app_type'],
            'appid' => $credentials['appid'],
            'wechat_openid' => $credentials['openid'],
            'wechat_unionid' => $credentials['unionid'] ?? null,
            'is_register' => false
        ]);
    }

    /**
     * 创建静默登录用户的设备信息
     *
     * @param $credentials
     * @return void
     */
    private function createLoginDeviceByInfo($credentials): void
    {
        $this->login_info->deviceInfo()->createMany([
            [
                'brand' => $credentials['device']['brand'],
                'model' => $credentials['device']['model'],
                'system' => $credentials['device']['system'],
                'platform' => $credentials['device']['platform'],
                'memory_size' => $credentials['device']['memorySize'],
                'SDK_version' => $credentials['app_base']['SDKVersion'],
                'language' => $credentials['app_base']['language'],
                'version' => $credentials['app_base']['version'],
                'theme' => $credentials['app_base']['theme'] ?? null,
                'font_size_scale_factor' => $credentials['app_base']['fontSizeScaleFactor'],
                'font_size_setting' => $credentials['app_base']['fontSizeSetting']
            ]
        ]);
    }

    private function createUserInfo($credentials)
    {
//        dd($this->login_info);
//        $this->login_info->userInfo()->create([
//            'user_id' => $this->login_info->user_id,
//            'level' => 1,
//            'integral' => 0
//        ]);
    }

    /**
     * 尝试检索当前用户是否进行了静默登录
     *
     * @return mixed
     */
    public function checkSilentUser(): mixed
    {
        return $this->redis->sismember('silent_login_user_token_set', $this->request->cookie($this->getRecallerName()));
    }

    /**
     * 尝试解密静默登录的登录信息
     *
     * @param $token
     * @return array
     */
    public function decryptSilentToken($token = null)
    {
        return explode('.', Crypt::decryptString($token ?? $this->request->cookie($this->getRecallerName())));
    }

    /**
     * 尝试使用给定的凭据对用户进行身份验证
     *
     * @param array $credentials
     * @return mixed
     */
//    public function attempt(array $credentials = [], string $silent_token = null)
    public function attempt(array $credentials = [], $remember = false)
    {
        $this->validate($credentials);

        $user = $this->lastAttempted;

        return $user->is_register ? $this->login($user) : ['is_register' => false, 'token' => null, 'info' => []];

        /**
         * [
         *   'phoneNumber' => '13811111111',
         *   'purePhoneNumber' => '13811111111',
         *   'countryCode' => '86',
         *   'watermark' => [
         *     'timestamp' => 1727673918,
         *     'appid' => 'wxd8bcfa43ca3fb256'
         *   ]
         * ]
         */
        $silent_token = $silent_token ?? $this->request->cookie($this->getRecallerName());

        [$app_type, $appid, $wechat_openid] = $this->decryptSilentToken($silent_token);

        $credentials['app_type'] = $app_type;
        $credentials['appid'] = $appid;
        $credentials['wechat_openid'] = $wechat_openid;

        $this->login_info = $this->retrieveSilentLoginInfoByCredentials([
            'wechat_openid' => $credentials['wechat_openid']
        ]);

        if (is_null($this->login_info)) {
            return null;
        }

        $user = $this->provider->retrieveByCredentials([
            'phone_number' => $credentials['purePhoneNumber']
        ]);

        // 无需验证密码
        if (is_null($user)) {
            $user = $this->register($credentials);
        }

        if (!$this->hasValidCredentials($user)) {
            $this->login($user);

            $this->removeSilentLoginUserToken($silent_token);

            return $this->token;
        }

        return null;
    }

    /**
     * 根据给定的凭据创建用户
     *
     * @param $credentials
     * @return mixed
     */
    protected function register($credentials)
    {
        $user = $this->provider->createModel()->create([
            'user_login_info_id' => $this->login_info->id,
            'phone_number' => $credentials['purePhoneNumber'],
            'phone_prefix' => $credentials['countryCode'],
            'last_login_ip' => $this->request->ip(),
            'is_login' => true,
            'is_freeze' => false
        ]);

        $user->info()->create([
            'level' => 1,
            'integral' => 0
        ]);

        return $user;
    }


    /**
     * 关联用户登录信息到注册用户
     *
     * @param $user
     * @return void
     */
    private function associateUserLoginInfoToUser($user)
    {
        $this->login_info->user_id = $user->id;
        $this->login_info->is_register = true;

        $this->login_info->save();
    }


    /**
     * 获取 redis 登录用户存储唯一标识符
     *
     * @return string
     */
    public function getName(string $name = null)
    {
        return 'wxr_wechat_' . ($name ?? $this->user->phone_number) . '_info';
    }

    /**
     * 删除静默登录使用的临时 token
     *
     * @param $token
     * @return void
     */
    private function removeSilentLoginUserToken($token)
    {
        $this->redis->srem('silent_login_user_token_set', $token);
    }

    /**
     * 获取当前已通过身份验证的用户
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // 如果我们已经为当前请求获取了用户数据，则可以立即返回
        // 我们不想在每次调用该方法时都获取用户数据，因为那样会非常慢
        if (!is_null($this->user)) {
            return $this->user;
        }

        $jwt = $this->app->request->bearerToken();

        $id = ($payload = $this->validateJsonWebToken($jwt)) ? $payload['sub'] : null;

        if (Redis::connection($this->redis_connection)->hget('user_login', $id) === $jwt) {
//            if (!is_null($id)) $this->user = $this->provider->retrieveByCredentials(['uid' => $id]);
            $user = Redis::connection($this->redis_connection)->hget($this->name . $id, 'laravel_ORM');

            if (!is_null($id)) $this->user = $user ? unserialize($user) : null;
        } else {
            return null;
        }

//        $recaller = $this->recaller();
//
//        $this->user = $this->userFromRecaller($recaller);

        return $this->user;
    }


    public function refreshTokenExpiredTime()
    {
        if (is_null($this->user)) {
            return false;
        }

        return $this->redis->expire($this->getName(), config('auth.password_timeout'));
    }


    private function setLoginInfo($credentials)
    {
        $this->login_info = ClientUserLoginInfo::where([
            'app_type' => $credentials['app_type'],
            'appid' => $credentials['appid'],
            'wechat_openid' => $credentials['wechat_openid']
        ])->first();
    }

    private function getLoginInfo()
    {
        return $this->login_info;
    }

    private function freshLoginInfo()
    {
        $this->login_info = $this->login_info->fresh();
    }


    protected function recaller()
    {
        return $this->request->cookie($this->getRecallerName());
    }

    protected function userFromRecaller($recaller)
    {
        if ($phone = $this->redis->hget('login_user_log', $recaller)) {
            if ($recaller == $this->redis->hget('login_user_token', $phone)) {
                if ($user = unserialize($this->redis->get($this->getName(name: $phone)))) {
                    return $user;
                }
            }
        }

        return null;
    }

    protected function fireLoginEvent($user)
    {
//        $this->events?->dispatch(new UserLogin($user));
        $this->events?->dispatch(new Login('wechat', $user, false));
    }

    private function setDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }

//    private function decryptSilentToken($token)
//    {
//        return [$app_type, $appid, $wechat_openid] = explode('.', Crypt::decryptString($token));
//    }

    /**
     * 获取认证会话值的唯一标识符
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'wxr_session_id';
    }

    public function validate(array $credentials = []): void
    {
        $retrieveCredentials = array_filter($credentials, fn($key) => in_array($key, ['openid', 'purePhoneNumber']), ARRAY_FILTER_USE_KEY);

        $user = null;

        if (isset($retrieveCredentials['purePhoneNumber'])) {
            $this->validatePhoneNumber($retrieveCredentials['purePhoneNumber']);
        } else {
            $user = $this->validateOpenId($retrieveCredentials['openid']);
        }

        if (!$this->hasValidCredentials($user)) {
            $user = $this->createUser($credentials);
        }

//        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($retrieveCredentials);
        $this->lastAttempted = $user;
    }

    protected function validateOpenId($openId): ?Authenticatable
    {
//        $credentials = ['open_ids->wechat_miniprogram' => function ($query) use ($openId) {
//            $query->whereJsonContains('open_ids->wechat_miniprogram', $openId);
//        }];

        $credentials = ['app_type' => 'wechat_miniprogram', 'appid' => config('wechat.miniprogram.app_id'), 'wechat_openid' => $openId];

        return $this->provider->setModel(ClientUserLoginInfo::class)->retrieveByCredentials($credentials);
    }

    protected function validatePhoneNumber($phoneNumber): ?Authenticatable
    {
        $credentials = ['phone_number' => $phoneNumber];

        return $this->provider->retrieveByCredentials($credentials);
    }

    /**
     * 检查账号是否被冻结
     *
     * @param $user
     * @return bool
     */
    protected function hasValidCredentials($user): bool
    {
        return !is_null($user);
    }

    protected function createUser($credentials)
    {
        return ClientUserLoginInfo::create([
            'app_type' => 'wechat_miniprogram',
            'appid' => config('wechat.miniprogram.app_id'),
            'wechat_openid' => $credentials['openid'],
            'is_register' => false
        ]);
    }


    public function once(array $credentials = [])
    {
        // TODO: Implement once() method.
    }

    public function loginUsingId($id, $remember = false)
    {
        // TODO: Implement loginUsingId() method.
    }

    public function onceUsingId($id)
    {
        // TODO: Implement onceUsingId() method.
    }

    public function viaRemember()
    {
        // TODO: Implement viaRemember() method.
    }

    public function logout()
    {
        // TODO: Implement logout() method.
    }

    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }
}