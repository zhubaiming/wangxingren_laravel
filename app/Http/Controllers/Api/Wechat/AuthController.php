<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\ClientUserResource;
use App\Models\ClientUser;
use App\Models\ClientUserLoginInfo;
use App\Services\Wechat\MiniAppServerSideService;
use App\Support\Traits\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiToken;

    public readonly string $name;

    public readonly string $redis_connection;

    public function __construct(MiniAppServerSideService $service)
    {
        $this->service = $service;

        $this->name = 'user:uid:';

        $this->redis_connection = 'wechat_user';
    }

    public function silentLogin(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $code_session = $this->service->code2session($validated['code']);

        $user = ClientUserLoginInfo::where('app_type', 'wechat_miniprogram')
            ->where('appid', config('wechat.miniprogram.app_id'))
            ->where('openid', $code_session['openid'])
            ->when(isset($code_session['unionid']), function ($query) use ($code_session) {
                return $query->where('unionid', $code_session['unionid']);
            })->with('user')->first();

        $payload = ['is_register' => false, 'token' => null, 'info' => []];

        if (is_null($user)) {
            ClientUserLoginInfo::create([
                'app_type' => 'wechat_miniprogram',
                'appid' => config('wechat.miniprogram.app_id'),
                'openid' => $code_session['openid'],
                'unionid' => $code_session['unionid'],
                'is_register' => false
            ]);
        } else {
            if (!is_null($user->user)) {
                $payload = ['is_register' => true, 'token' => Auth::guard('wechat')->login($user->user), 'info' => (new ClientUserResource($user))->additional(['format' => __FUNCTION__])];
            }
        }

        return $this->success(arrLineToHump($payload));
    }

    public function registerLogin(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        $code_session = $this->service->code2session($validated['code_login']);

        $phone_info = $this->service->getPhoneNumber($validated['code_phone'], $code_session['openid']);

        $user = ClientUser::where('phone_number', $phone_info['purePhoneNumber'])
            ->where('phone_prefix', $phone_info['countryCode'])->first();

        if (is_null($user)) {
            $user = ClientUser::create([
                'uid' => strval(Str::ulid()),
                'phone_number' => $phone_info['purePhoneNumber'],
                'phone_prefix' => $phone_info['countryCode']
            ]);

            ClientUserLoginInfo::where('app_type', 'wechat_miniprogram')
                ->where('appid', config('wechat.miniprogram.app_id'))
                ->where('openid', $code_session['openid'])
                ->when(isset($code_session['unionid']), function ($query) use ($code_session) {
                    return $query->where('unionid', $code_session['unionid']);
                })->update(['user_id' => $user->id]);
        }

        $user->with('loginInfo')->fresh();

        $token = Auth::guard('wechat')->login($user);

        $payload = ['is_register' => true, 'token' => $token, 'info' => (new ClientUserResource($user))->additional(['format' => __FUNCTION__])];

        return $this->success(arrLineToHump($payload));
    }
}
