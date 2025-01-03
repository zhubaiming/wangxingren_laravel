<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Http\Controllers\Controller;
use App\Models\ClientUserPoll;
use App\Models\Coupon;
use App\Models\System;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function appIndexShow()
    {
        $payload = System::select('key', 'value')->where('key', 'APP_BANNER')->orWhere('key', 'APP_INDEX')->get();

        [$app_banner, $app_index] = $payload->toArray();

        $app_banner['value'] = json_decode($app_banner['value'], true);

        return $this->success(arrLineToHump(compact('app_banner', 'app_index')));
    }

    public function appPollIndex()
    {
        $payload = System::select('key', 'value')->where('key', 'APP_POLL')->first()->toArray();

        $payload['value'] = json_decode($payload['value'], true);

        return $this->success(arrLineToHump($payload));
    }

    public function appPollStore(Request $request)
    {
        if (0 === ClientUserPoll::where('user_id', Auth::guard('wechat')->user()->id)->count('id')) {
            Auth::guard('wechat')->user()->poll()->createQuietly(['value' => json_encode($request->input(), JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);

            $coupons = Coupon::where('related_action', 'questionnaire')->get();

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
                Auth::guard('wechat')->user()->coupons()->createManyQuietly($couponData);
            }
        }

        return $this->success();
    }
}
