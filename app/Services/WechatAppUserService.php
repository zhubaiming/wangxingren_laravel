<?php

namespace App\Services;

use App\Models\Wechat\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class WechatAppUserService
{
    private $table = 'wechat_users';
    private $user;

    public function handleLogin(array $data)
    {
        try {
            $user = User::where([
                'wechat_appid' => config('wechat.miniprogram.app_id'),
                'wechat_openid' => $data['openid']
            ])->canLogin()->firstOrFail();

            $this->setUser($user);
        } catch (ModelNotFoundException $e) {
            $this->handleRegistration($data);
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    private function handleRegistration(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                DB::table($this->table)->insert([
                    'wechat_appid' => config('wechat.miniprogram.app_id'),
                    'wechat_openid' => $data['openid'],
                    'wechat_unionid' => $data['unionid'] ?? null,
                    'wechat_session_key' => $data['session_key'],
                    'phone_prefix' => $data['countryCode'],
                    'phone_number' => $data['purePhoneNumber']
                ]);
            });
        } catch (UniqueConstraintViolationException $e) {
            // unique 索引重复导致创建失败
            DB::table($this->table)->where([
                'wechat_appid' => config('wechat.miniprogram.app_id'),
                'wechat_openid' => $data['openid'],
                'wechat_unionid' => $data['unionid'] ?? null,
                'wechat_session_key' => $data['session_key'],
                'phone_prefix' => $data['countryCode'],
                'phone_number' => $data['purePhoneNumber']
            ])->update([
                'is_freeze' => false,
                'is_del' => false
            ]);
        }

        $this->handleLogin($data);
//        $user = User::firstOrNew(
//            [
//                'wechat_appid' => config('wechat.miniprogram.app_id'),
//                'wechat_openid' => $data['openid']
//            ],
//            [
//                'wechat_unionid' => $data['unionid'] ?? null,
//                'wechat_session_key' => $data['session_key'],
//                'phone_prefix' => $data['countryCode'],
//                'phone_number' => $data['purePhoneNumber']
//            ]
//        );
//
//        if ($user->phone_prefix != $data['countryCode'] || $user->phone_number != $data['purePhoneNumber']) {
//            $user->phone_prefix = $data['countryCode'];
//            $user->phone_number = $data['purePhoneNumber'];
//        }
//
//        $user->save();
//
//        $this->setUser($user);
    }


    private function handleFreshUser($user)
    {
        Redis::set('wechatApp_login_' . $user->token, $user->toJson());
    }


    private function setUser($user)
    {
        $token = $user->token ?? $this->handleTokenGenerate();

        $user->token = $token;

        if (0 == Redis::exists('wechatApp_login_' . $token)) {
            Redis::setnx('wechatApp_login_' . $token, $user->toJson());
        } else {
            $this->handleFreshUser($user);
        }

        $this->user = $user;
    }

    private function handleTokenGenerate()
    {
        $key = config('app.key');

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return hash_hmac('sha256', Str::random(40), $key);
    }
}