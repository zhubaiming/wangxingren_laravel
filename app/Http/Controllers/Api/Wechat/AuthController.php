<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\ClientUserResource;
use App\Services\Wechat\MiniAppServerSideService;
use App\Support\Traits\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiToken;

    public function __construct(MiniAppServerSideService $service)
    {
        $this->service = $service;
    }

    public function silentLogin(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $code_session = $this->service->code2session($validated['code']);

        $credentials = [
            'attributes' => [
                'app_type' => 'wechat_miniprogram',
                'appid' => config('wechat.miniprogram.app_id'),
                'openid' => $code_session['openid']
            ],
            'data' => [
                'unionid' => $code_session['unionid'] ?? null,
                'is_register' => false,
                'device' => $validated['device'],
                'system' => $validated['app_base']
            ],
            'func' => __FUNCTION__
        ];

        if (isset($code_session['unionid'])) {
            $credentials['attributes']['unionid'] = $code_session['unionid'];
        }

        $payload = Auth::guard('wechat')->attempt($credentials);

        if (!empty($payload['info'])) {
            $authDefault = Auth::guard('wechat')->user()->with([
                'pets' => function ($query) {
                    $query->where('is_default', true);
                },
                'addresses' => function ($query) {
                    $query->where('is_default', true);
                }
            ])->first();

            $payload['info']->pets = $authDefault->pets;
            $payload['info']->addresses = $authDefault->addresses;

            $payload['info'] = (new ClientUserResource($payload['info']))->additional(['format' => __FUNCTION__]);
        }

        return $this->success(arrLineToHump($payload));
    }

    public function registerLogin(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $code_session = $this->service->code2session($validated['code_login']);

        $phone_info = $this->service->getPhoneNumber($validated['code_phone'], $code_session['openid']);

        $credentials = [
            'attributes' => [
                'phone_prefix' => $phone_info['countryCode'],
                'phone_number' => $phone_info['purePhoneNumber']
            ],
            'data' => [
                'uid' => strval(Str::ulid())
            ],
            'extra' => [
                'openid' => $code_session['openid'],
            ],
            'func' => __FUNCTION__
        ];

        if (isset($code_session['unionid'])) {
            $credentials['extra']['unionid'] = $code_session['unionid'];
        }

        $payload = Auth::guard('wechat')->attempt($credentials);

        if (!empty($payload['info'])) {
            $payload['info'] = (new ClientUserResource($payload['info']))->additional(['format' => __FUNCTION__]);
        }

        return $this->success(arrLineToHump($payload));
    }

    public function logout()
    {
        Auth::guard('wechat')->logout();

        return $this->success();
    }

    public function info()
    {
        $payload = Auth::guard('wechat')->user()->with([
            'pets' => function ($query) {
                $query->where('is_default', true);
            },
            'addresses' => function ($query) {
                $query->where('is_default', true);
            }
        ])->first();

        return $this->success($this->returnIndex($payload, 'Wechat/UserInfoResource', __FUNCTION__, false));
    }
}
