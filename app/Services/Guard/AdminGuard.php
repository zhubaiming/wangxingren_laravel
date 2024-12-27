<?php

namespace App\Services\Guard;

use App\Support\Traits\ApiToken;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Redis;

class AdminGuard implements StatefulGuard
{
    use GuardHelpers, ApiToken;

    public readonly string $name;

    public readonly string $redis_connection;

    private $app;

    protected $lastAttempted;


//    public function attempt(array $credentials = [])
//    {
//        $user = $this->provider->retrieveByCredentials($credentials);
//    }


    public function __construct($app, UserProvider $provider)
    {
        $this->name = 'user:uid:';

        $this->redis_connection = 'admin_user';

        $this->app = $app;

        $this->provider = $provider;
    }


    public function __invoke() // 把实例当作方法来调用
    {

    }

    public function user(): ?Authenticatable
    {
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

        return $this->user;
    }

    public function validate(array $credentials = []): bool
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        return $this->hasValidCredentials($user, $credentials);
    }


    public function setUser(Authenticatable $user): AdminGuard|static
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

    /**
     * 该方法主要结合了，验证、登录的流程，不用外部分开调用，如果不想使用此方法，也可以自己在外部分开调用validate()和login()
     */
    public function attempt(array $credentials = [], $remember = false): false|string
    {
        if ($this->validate($credentials)) {
            $user = $this->lastAttempted;

            return $this->login($user);
        }

        return false;
    }

    public function once(array $credentials = [])
    {
        // TODO: Implement once() method.
    }

    public function login(Authenticatable $user, $remember = false): string
    {
        // getAuthIdentifier 返回用户的「主键」
        //  $this->updateSession($user->getAuthIdentifier());
        $token = $this->updateToken($user->getAuthIdentifier());

        /*
        // If the user should be permanently "remembered" by the application we will
        // queue a permanent cookie that contains the encrypted copy of the user
        // identifier. We will then decrypt this later to retrieve the users.
        if ($remember) {
            $this->ensureRememberTokenIsSet($user);

            $this->queueRecallerCookie($user);
        }

        // If we have an event dispatcher instance set we will fire an event so that
        // any listeners will hook into the authentication events and run actions
        // based on the login and logout events fired from the guard instances.
        $this->fireLoginEvent($user, $remember);
        */

        $this->setUser($user);

        return $token;
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

    public function logout(): void
    {
        $user = $this->user();

        $this->clearUserDataFromStorage($user->getAuthIdentifier());

        $this->user = null;
    }

    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    protected function hasValidCredentials($user, $credentials): bool
    {
        $validated = !is_null($user) && $this->provider->validateCredentials($user, $credentials);

        return $validated;
    }

    protected function updateToken($primaryKey): string
    {
        $token = $this->generateJsonWebToken([
            'sub' => $primaryKey
        ]);

        Redis::connection($this->redis_connection)->hset('user_login', $primaryKey, $token);

        return $token;
    }

    protected function clearUserDataFromStorage($primaryKey): void
    {
        Redis::connection($this->redis_connection)->hdel('user_login', $primaryKey);
        Redis::connection($this->redis_connection)->del($this->name . $primaryKey);
    }
}