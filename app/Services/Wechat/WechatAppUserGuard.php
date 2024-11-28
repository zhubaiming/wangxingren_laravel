<?php

namespace App\Services\Wechat;

use App\Models\ClientUserDeviceInfo;
use App\Models\ClientUserLoginInfo;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * 自定义守卫
 *
 * 在 AppServiceProvider 中使用 extend 定义
 *
 * 传递给 extend 方法的回调函数应该返回 Illuminate\Contracts\Auth\Guard
 */
class WechatAppUserGuard
{
    use GuardHelpers;

    private $app;

    private $redis;

    private $request;

    private $uuid;

    private $events;

    private $token;

    private $login_info;

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
        $this->app = $app;

        $this->redis = $app['redis']->connection('wechat_user');

        $this->request = $app['request'];

        $this->provider = $provider;
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
    public function attempt(array $credentials = [], string $silent_token = null)
    {
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
     * 检查账号是否被冻结
     *
     * @param $user
     * @return bool
     */
    protected function hasValidCredentials($user)
    {
        return $user->is_freeze;
    }

    /**
     * 使用给定的用户进行登录
     *
     * @param $user
     * @return void
     */
    public function login($user)
    {
        $this->associateUserLoginInfoToUser($user);

        $user = $user->fresh();

        $this->fireLoginEvent($user);

        $this->setUser($user);

        $this->updateToken();
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
     * 根据当前需要登录的用户生成单一设备登录对应关系，同时存储用户信息，并生成登录用户的 token
     *
     * @return void
     */
    protected function updateToken()
    {
        $this->token = Str::uuid()->toString();

        $this->redis->hset('login_user_token', $this->user->phone_number, $this->token);
        $this->redis->hset('login_user_log', $this->token, $this->user->phone_number);

        // database 和 Eloquent 均支持
//        $this->redis->set($this->getName(), base64_encode(gzcompress(serialize($this->user))));
//        dd(unserialize(gzuncompress(base64_decode($this->redis->get($this->getName())))));

        $this->redis->setex($this->getName(), config('auth.password_timeout'), serialize($this->user));

        // database 不支持 Eloquent 支持
//        $this->redis->set($this->getName(), json_encode($this->user));
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

        $recaller = $this->recaller();

        $this->user = $this->userFromRecaller($recaller);

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
}