<?php

namespace App\Services;

use App\Services\Order\Plugins\GenerateOrderNoPlugin;
use App\Services\Order\Plugins\GenerateTransactionsNoPlugin;
use App\Services\Order\Plugins\MessagePushPlugin;
use App\Services\Order\Plugins\PushDelayCancelQueuePlugin;
use App\Services\Order\Plugins\SaveOrderDataPlugin;
use App\Services\Order\Plugins\SaveTransactionsDataPlugin;
use App\Services\Order\Plugins\ValidateCollectsReasonablyPlugin;
use App\Services\Order\Plugins\ValidateTimeIsLockPlugin;
use Carbon\CarbonImmutable;
use Hongyi\Designer\Vaults;

readonly class OrderService
{
    private \DateTimeInterface $initTime;

    private \DateTimeInterface $time;

    public function __construct()
    {
        $this->initTime = CarbonImmutable::create(2024, 10, 8, 0, 0, 0, config('app.timezone'));

        $this->time = CarbonImmutable::now(config('app.timezone'));
    }

    public function create(array $collects, int $user_id, ?string $type = null)
    {
        return Vaults::handle([
            ValidateTimeIsLockPlugin::class, // 校验预约时间是否锁定
            ValidateCollectsReasonablyPlugin::class, // 检验订单参数合理性
            GenerateOrderNoPlugin::class, // 生成订单编号
            GenerateTransactionsNoPlugin::class, // 生成流水号
            SaveOrderDataPlugin::class, // 保存订单
            SaveTransactionsDataPlugin::class, // 保存流水
            PushDelayCancelQueuePlugin::class, // 订单支付超时事件队列推送
            MessagePushPlugin::class // 订单消息推送
        ], [
            'init_time' => $this->initTime,
            'time' => $this->time,
            'user_id' => $user_id,
            'type' => $type,
            'collects' => $collects,
        ], false);
    }

    public function cannel()
    {

    }

    public function requestRefund()
    {

    }

    public function pay()
    {

    }

    public function notifyPay()
    {

    }

    public function refund()
    {

    }

    public function notifyRefund()
    {

    }
}
