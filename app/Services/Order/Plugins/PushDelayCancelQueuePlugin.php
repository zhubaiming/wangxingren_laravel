<?php

namespace App\Services\Order\Plugins;

use App\Enums\OrderStatusEnum;
use App\Jobs\DelayCannelOrderJob;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

class PushDelayCancelQueuePlugin implements PluginInterface
{

    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();

        if ($parameters['status'] === OrderStatusEnum::paying) {
            // 订单流入队列，进行15分钟未支付取消的判断
            DelayCannelOrderJob::dispatch($parameters['order_no'])->delay($parameters['expected_times']);
        }

        return $next($patchwerk);
    }
}
