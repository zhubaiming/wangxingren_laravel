<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\ClientUserInfo;
use App\Models\ClientUserOrder;
use Carbon\CarbonImmutable;

class HomeController extends Controller
{
    public function info()
    {
        $now = CarbonImmutable::now(config('app.timezone'));
        $time = $now->toDateString() . '  ' . $now->isoFormat('A') . '好';

        $client_user_infos = ClientUserInfo::select('is_register')->get();
        $client_user_orders = ClientUserOrder::select('payer_total', 'pay_channel', 'created_at', 'reservation_date', 'pay_success_at')->where('status', OrderStatusEnum::finishing)->orWhere('status', OrderStatusEnum::finished)->get();

        // 使用人数
        $client_user_count = $client_user_infos->count();

        // 注册人数
        $client_user_register_count = $client_user_infos->filter(fn($item) => $item['is_register'])->count();

        // 今日下单量
        $client_user_order_today_count = $client_user_orders->filter(fn($item) => CarbonImmutable::parse($item['created_at'], config('app.timezone'))->isToday())->count();

        // 今日预约单数
        $client_user_order_today_reservation_count = $client_user_orders->filter(fn($item) => CarbonImmutable::parse($item['reservation_date'], config('app.timezone'))->isToday())->count();

        // 当季销售额
        $client_user_order_season_total = applyIntegerToFloatModifier($client_user_orders->filter(fn($item) => isset($item['pay_success_at']) && CarbonImmutable::parse($item['pay_success_at'], config('app.timezone'))->isCurrentQuarter())->sum('payer_total'));

        // 当月销售额
        $client_user_order_month_total = applyIntegerToFloatModifier($client_user_orders->filter(fn($item) => isset($item['pay_success_at']) && CarbonImmutable::parse($item['pay_success_at'], config('app.timezone'))->isCurrentMonth())->sum('payer_total'));

        // 当日销售额
        $client_user_order_day_total = applyIntegerToFloatModifier($client_user_orders->filter(fn($item) => isset($item['pay_success_at']) && CarbonImmutable::parse($item['pay_success_at'], config('app.timezone'))->isToday())->sum('payer_total'));


        return $this->success(arrLineToHump(compact('time', 'client_user_count', 'client_user_register_count', 'client_user_order_today_count', 'client_user_order_today_reservation_count', 'client_user_order_season_total', 'client_user_order_month_total', 'client_user_order_day_total')));
    }
}