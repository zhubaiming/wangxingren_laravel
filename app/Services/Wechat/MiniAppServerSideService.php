<?php

namespace App\Services\Wechat;

use App\Support\Traits\HttpClient;
use Illuminate\Support\Facades\Redis;

class MiniAppServerSideService
{
    use HttpClient;

    private $base_url = 'https://api.weixin.qq.com/';

    /**
     * 小程序登录
     *
     * @param string $js_code
     * @return null
     */
    public function code2session(string $js_code)
    {
        // GET 调用
        $url = $this->joinUrl(config('wechat.miniprogram.server.code2session.uri'));

        $parameters = [
            'appid' => config('wechat.miniprogram.app_id'),      // 小程序 appId
            'secret' => config('wechat.miniprogram.app_secret'), // 小程序 appSecret
            'js_code' => $js_code,                                // 登录时获取的 code，可通过wx.login获取
            'grant_type' => 'authorization_code'                  // 授权类型，此处只需填写 authorization_code
        ];

        return $this->sendRequest($url, config('wechat.miniprogram.server.code2session.method'), $parameters);
    }

    /**
     * 支付后获取 Unionid
     *
     * @param array $credentials
     * @return null
     */
    public function getPaidUnionid(array $credentials)
    {
        // GET 调用
        $url = $this->joinUrl(config('wechat.miniprogram.server.getPaidUnionid.uri'));

        $parameters = [
            'access_token' => $this->useAccessToken(),          // 接口调用凭证
            'openid' => $credentials['openid'],                 // 支付用户唯一标识
            'transaction_id' => $credentials['transaction_id'], // 微信支付订单号(非必填)
            'mch_id' => $credentials['mch_id'],                 // 微信支付分配的商户号，和商户订单号配合使用(非必填)
            'out_trade_no' => $credentials['out_trade_no']      // 微信支付商户订单号，和商户号配合使用(非必填)
        ];

        return $this->sendRequest($url, config('wechat.miniprogram.server.getPaidUnionid.method'), $parameters);
    }

    /**
     * 获取手机号
     *
     * @param string $code
     * @return null
     */
    public function getPhoneNumber(string $code, string $openid = null)
    {
        // POST 调用
        $url = $this->joinUrl(config('wechat.miniprogram.server.getPhoneNumber.uri'));

        $parameters = [
            'access_token' => $this->useAccessToken() // 接口调用凭证
        ];

        $data = [
            'code' => $code,    // 手机号获取凭证
            'openid' => $openid // 支付用户唯一标识(非必填)
        ];

        return $this->sendRequest($url, config('wechat.miniprogram.server.getPhoneNumber.method'), $parameters, $data)['phone_info'];
    }

    /**
     * 获取接口调用凭据
     *
     * @return null
     */
    private function getAccessToken()
    {
        // GET 调用
        $url = $this->joinUrl(config('wechat.miniprogram.server.getAccessToken.uri'));

        $parameters = [
            'grant_type' => 'client_credential',                 // 授权类型，此处只需填写 client_credential
            'appid' => config('wechat.miniprogram.app_id'),     // 小程序 appId
            'secret' => config('wechat.miniprogram.app_secret') // 小程序 appSecret
        ];

        return $this->sendRequest($url, config('wechat.miniprogram.server.getAccessToken.method'), $parameters);
    }

    /**
     * 获取稳定版接口调用凭据
     *
     * @param $force_refresh
     * @return null
     */
    private function getStableAccessToken($force_refresh = false)
    {
        // POST 调用
        $url = $this->joinUrl(config('wechat.miniprogram.server.getStableAccessToken.uri'));

        $data = [
            'grant_type' => 'client_credential',                  // 授权类型，此处只需填写 client_credential
            'appid' => config('wechat.miniprogram.app_id'),      // 小程序 appId
            'secret' => config('wechat.miniprogram.app_secret'), // 小程序 appSecret
            'force_refresh' => $force_refresh                     // 默认使用 false。1. force_refresh = false 时为普通调用模式，access_token 有效期内重复调用该接口不会更新 access_token；2. 当force_refresh = true 时为强制刷新模式，会导致上次获取的 access_token 失效，并返回新的 access_token
        ];

        return $this->sendRequest($url, config('wechat.miniprogram.server.getStableAccessToken.method'), data: $data);
    }

    /**
     * 获取并使用凭据
     *
     * @return mixed
     */
    private function useAccessToken()
    {
        if (app()->isLocal()) {
            $key = 'local_wechat_mini_app_access_token';
            $func = 'getAccessToken';
        } else {
            $key = 'wechat_mini_app_access_token';
            $func = 'getStableAccessToken';
        }

        if (Redis::exists($key)) {
            return Redis::connection('cache')->get($key);
        } else {
            $access_token = $this->{$func}();
            Redis::connection('cache')->setex($key, intval(bcsub($access_token['expires_in'], 100, 0)), $access_token['access_token']);
            return $access_token['access_token'];
        }
    }
}