<?php

namespace App\Services\Order\Plugins;

use App\Enums\OrderStatusEnum;
use App\Enums\PayChannelEnum;
use App\Models\ClientUserCoupon;
use App\Models\ClientUserOrder;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

class SaveOrderDataPlugin implements PluginInterface
{

    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();

        $expected_times = $parameters['time']->addSeconds(300);

        $status = max($parameters['collects']['payer_total'], 0) === 0 || in_array($parameters['collects']['pay_channel'], PayChannelEnum::getOffLineChannels()) ? OrderStatusEnum::finishing : OrderStatusEnum::paying;

        $collects = [
            ...$parameters['collects'],
            'order_no' => $parameters['order_no'],
            'trade_no' => $parameters['trade_no'],
            'user_id' => $parameters['user_id'],
            'status' => $status->value,
            'expected_at' => $expected_times->toDateTimeString()
        ];

        if (!empty($parameters['collects']['coupon_json'])) $this->usedCoupon($parameters['collects']['coupon_json']);

        ClientUserOrder::create($collects, $parameters['type']);

        $patchwerk->mergeParameters([
            'expected_times' => $expected_times,
            'status' => $status
        ]);

        $patchwerk->setDestination([
            'trade_no' => $parameters['trade_no'],
            'payer_total' => $parameters['collects']['payer_total']
        ]);

        return $next($patchwerk);
    }

    private function usedCoupon($coupon)
    {
        ClientUserCoupon::where('id', $coupon['id'])->update(['status' => false]);
    }
}
