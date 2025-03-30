<?php

namespace App\Services\Guard;

use App\Models\ClientUser;
use App\Models\ClientUserInfo;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

/**
 * 自定义守卫
 *
 * 在 AppServiceProvider 中使用 extend 定义
 *
 * 传递给 extend 方法的回调函数应该返回 Illuminate\Contracts\Auth\Guard
 */
class WechatMiniprogramGuard implements StatefulGuard
{
    /**
     * 刷新间隔
     * @var int
     */
    private int $refreshInterval = 7200;

    /**
     * 用于生成 token 的哈希算法名称
     * @var string
     */
    private string $redisKeyAgo = 'sha256';

    /**
     * 当前 token 值
     * @var string
     */
    private string $token;

    /**
     * App 容器实例
     * @var mixed
     */
    private $app;

    /**
     * Redis 实例
     * @var Connection
     */
    private $redis;

    /**
     * 用户实例
     * @var Authenticatable|null
     */
    public $user = null;

    public function __construct($app)
    {
        $this->app = $app;

        $this->redis = Redis::connection('wechat_user')->client();
    }

    /**
     * 确定当前用户是否已通过身份验证
     * 检查当前请求的 token 是否有效，若过期则尝试刷新
     * @return bool
     */
    public function check(): bool
    {
        $token = $this->app->request->bearerToken();
        if (empty($token)) {
            $this->app->request->merge(['http_error' => 'CLIENT_HTTP_UNAUTHORIZED']);
            return false;
        }

        // 检查 Redis 中是否存在 token
        if (!$this->redis->exists('user:token:' . $token)) {
            $this->app->request->merge(['http_error' => 'CLIENT_HTTP_UNAUTHORIZED_EXPIRED']);
            return false;
        }

        $this->token = $token;
        $user = $this->userInfo();
        if (is_null($user)) {
            $this->app->request->merge(['http_error' => 'CLIENT_HTTP_AUTHORIZED_ILLEGAL']);
            return false;
        }

        // 校验 token 是否为用户最新 token
        $latestToken = $this->redis->get('user:latest_token:' . $user['uid']);
        if ($latestToken !== $token) {
            $this->app->request->merge(['http_error' => 'CLIENT_HTTP_UNAUTHORIZED_BLACKLISTED']);
            return false;
        }

        // 判断当前时间是否大于或等于 token 刷新时间
        if (Carbon::now()->timestamp >= $user['last_token_expire_time']) {
            return $this->updateToken($user);
        }

        $this->setToken($token);
        return true;
    }

    /**
     * 确定当前用户是否为访客
     * @inheritDoc
     */
    public function guest()
    {
        // TODO: Implement guest() method.
    }

    /**
     * 获取当前登录用户数据
     * @return Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $userInfo = $this->userInfo();

        $user = ClientUser::where('uid', $userInfo['uid'])->with('info', function ($query) use ($userInfo) {
            $query->where('appid', config('wechat.miniprogram.app_id'))->where('openid', $userInfo['openid']);
        })->first();

        if (!is_null($user)) {
            $user->info = $user->info->first();
        }

        $this->user = $user;

        return $this->user;
    }

    /**
     * 获取当前登录用户数据
     * @return array|null
     */
    private function userInfo(): ?array
    {
        $userData = $this->redis->get('user:token:' . $this->token);
        return $userData ? json_decode($userData, true) : null;
    }

    /**
     * @inheritDoc
     */
    public function id()
    {
        // TODO: Implement id() method.
    }

    /**
     * @inheritDoc
     */
    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
    }

    /**
     * @inheritDoc
     */
    public function hasUser()
    {
        // TODO: Implement hasUser() method.
    }

    /**
     * 设置当前用户
     * 设置当前用户数据到 Redis
     * @param Authenticatable $user
     * @return void
     */
    public function setUser(Authenticatable $user): void
    {
        $data = [
            'uid' => $user->uid,
            'nick_name' => $user->nick_name,
            'avatar' => $user->avatar,
            'openid' => $user->info->openid,
            'is_register' => $user->info['is_register'],
            // 下次 token 刷新时间: 当前时间 + refreshInterval 秒数
//            'last_token_expire_time' => Carbon::now()->addSeconds(10)->timestamp
            'last_token_expire_time' => Carbon::now()->addSeconds($this->refreshInterval)->timestamp
        ];

        $json = json_encode($data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // 先获取旧 token
        $oldToken = $this->redis->get('user:latest_token:' . $user->uid);

        // 删除旧 token(保证单设备登录)
        if ($oldToken) {
            $this->redis->del('user:token:' . $oldToken);
        }

//        $this->redis->setex($this->token, $this->validityPeriod, $json);
        $this->redis->set('user:token:' . $this->token, $json);
        $this->redis->set('user:latest_token:' . $user->uid, $this->token);

        $this->user = $user;
    }

    /**
     * 验证
     * @param array $credentials
     * @param $remember
     * @return bool|array
     */
    public function attempt(array $credentials = [], $remember = false): bool|array
    {
        $result = ['is_register' => false, 'token' => null, 'info' => []];

        $user = $this->{'attempt' . ucfirst($credentials['func'])}($credentials);

        if (!is_null($user)) {
            $result = ['is_register' => $user->info->is_register, 'token' => $this->login($user), 'info' => $user];
        }

        return $result;
    }

    private function attemptSilentLogin($credentials)
    {
        $user = ClientUserInfo::with('user')->firstOrCreate($credentials['attributes'], $credentials['data']);

        $user->device = $credentials['data']['device'];
        $user->system = $credentials['data']['system'];
        $user->save();

        if (!is_null($user->user)) {
            $user->user->info = $user;

            return $user->user;
        }

        return null;
    }

    private function attemptRegisterLogin($credentials)
    {
        $sendCoupon = 0 === ClientUser::where('phone_number', $credentials['attributes']['phone_number'])->count('id');

        $user = ClientUser::with(['info' => function ($query) use ($credentials) {
            $query->where('appid', config('wechat.miniprogram.app_id'))->where('openid', $credentials['extra']['openid']);
        }])->firstOrCreate($credentials['attributes'], $credentials['data']);

        if ($sendCoupon) {
            $coupons = Coupon::where('related_action', 'registered')->get();

            $couponData = [];
            $time = Carbon::now();
            foreach ($coupons as $coupon) {
                $couponData[] = [
                    'coupon_code' => $coupon->code,
                    'code' => $time->year . $time->month . $time->day . random_int(100000, 999999) . Auth::guard('wechat')->user()->id,
                    'title' => $coupon->title,
                    'amount' => $coupon->amount,
                    'min_total' => $coupon->min_total,
                    'description' => $coupon->description,
                    'expiration_at' => $coupon->expiration_at,
                    'status' => false,
                    'is_get' => false,
                ];
            }

            if (!empty($couponData)) {
                $user->coupons()->createManyQuietly($couponData);
            }
        }

        if (0 === count($user->info)) {
            ClientUserInfo::where('app_type', 'wechat_miniprogram')
                ->where('appid', config('wechat.miniprogram.app_id'))
                ->where('openid', $credentials['extra']['openid'])
                ->when(isset($credentials['extra']['unionid']), function ($query) use ($credentials) {
                    return $query->where('unionid', $credentials['extra']['unionid']);
                })->update(['user_id' => $user->id, 'is_register' => true]);

            $user->refresh()->load('info');
        }

        $user->info = $user->info->first();

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function once(array $credentials = [])
    {
        // TODO: Implement once() method.
    }

    /**
     * 登录时触发，保存用户信息并生成 token
     * @param Authenticatable $user
     * @param $remember
     * @return string
     */
    public function login(Authenticatable $user, $remember = false): string
    {
        $this->setToken($this->generateToken($user->getAuthIdentifier()));

        $this->setUser($user);

        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function loginUsingId($id, $remember = false)
    {
        // TODO: Implement loginUsingId() method.
    }

    /**
     * @inheritDoc
     */
    public function onceUsingId($id)
    {
        // TODO: Implement onceUsingId() method.
    }

    /**
     * @inheritDoc
     */
    public function viaRemember()
    {
        // TODO: Implement viaRemember() method.
    }

    /**
     * @inheritDoc
     */
    public function logout()
    {
        $token = $this->app->request->bearerToken();

        $this->setToken($token);

        $user = $this->userInfo();

        $this->redis->del('user:token:' . $token);
        $this->redis->del('user:latest_token:' . $user['uid']);

        return true;
    }

    /**
     * 更新 token，采用 Redis 分布式锁机制放置并发冲突
     * @param $user
     * @return bool
     */
    private function updateToken($user): bool
    {
        $oldToken = $this->token;
        $lockKey = 'lock:user:refresh:' . $oldToken;
        $lockValue = uniqid('', true); // 生成一个唯一的锁值
        $lockTTL = 10; // 锁的超时时间(秒)

        // 尝试获取锁
        if (!$this->acquireLock($lockKey, $lockValue, $lockTTL)) {
            // 如果获取锁失败，可以选择直接返回 false 或等待锁释放后重试
            // 这里采用等待锁释放
            while ($this->redis->exists($lockKey)) {
                sleep(1);
            }

            return true;
        }

        try {
            // 进入临界区，执行 token 更新逻辑
            // 检查是否已刷新 token
            if ($this->redis->exists('user:refresh_token') && $this->redis->sismember('user:refresh_token', $oldToken)) {
                return false;
            }

            // 生成新的 token
            $newToken = $this->generateToken($user['uid']);

            // 将就 token 标记为已刷新，并设置过期时间
            $this->redis->sadd('user:refresh_token', $oldToken);
            $this->redis->expire('user:token:' . $oldToken, 60);

            // 更新 token 和用户数据
            $this->setToken($newToken);

            $userModel = ClientUser::where('uid', $user['uid'])->with('info', function ($query) use ($user) {
                $query->where('openid', $user['openid']);
            })->first();
            $userModel->info = $userModel->info->first();

            $this->setUser($userModel);
        } finally {
            // 无论任务成功与否，都释放锁
            $this->releaseLock($lockKey, $lockValue);
        }

        return true;
    }

    /**
     * 尝试获取 Redis 锁
     */
    private function acquireLock($lockKey, $lockValue, $ttl): bool
    {
        // 使用 Redis SET 命令加上 NX(不存在时设置) 和 EX(设置过期时间)
        $result = $this->redis->set($lockKey, $lockValue, ['nx', 'ex' => $ttl]);
        return $result !== false;
    }

    /**
     * 释放 Redis 锁
     */
    private function releaseLock($lockKey, $lockValue): bool
    {
        // 使用 Lua 脚本确保只有持有锁的客户端才能删除锁
        $script = <<<'LUA'
            if redis.call("get", KEYS[1]) == ARGV[1] then
                return redis.call("del", KEYS[1])
            else
                return 0
            end
        LUA;

        $result = $this->redis->eval($script, [$lockKey, $lockValue], 1);
        return $result === 1;
    }

    /**
     * 生成新的 token
     * @param $primaryKey
     * @return string
     * @throws \Random\RandomException
     */
    private function generateToken($primaryKey): string
    {
        return hash($this->redisKeyAgo, $primaryKey . bin2hex(random_bytes(20)));
    }

    /**
     * 设置当前 token 值
     * @param $token
     * @return void
     */
    private function setToken($token): void
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}