<?php

namespace App\Services\Order\Plugins;

use App\Enums\PayChannelEnum;
use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Services\TradeDateService;
use Carbon\Carbon;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;
use Illuminate\Support\Facades\Redis;

class ValidateTimeIsLockPlugin implements PluginInterface
{

    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();

        $reservation_date_format = $parameters['collects']['reservation_date']->format('Y-m-d');

        if ($parameters['collects']['reservation_date']->lt(Carbon::today())) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '预约日期非法');
        }

        $redis_lock_key = 'reservation_date_' . $reservation_date_format . '-' . $parameters['collects']['reservation_car'];

        // 校验预约时间是否被锁定
        if (0 !== Redis::connection('order')->exists($redis_lock_key)) {
            if (!TradeDateService::checkTimeRange(
                ['start' => $parameters['collects']['reservation_time_start'], 'end' => $parameters['collects']['reservation_time_end']],
                array_map(fn($item) => json_decode($item, true), Redis::connection('order')->lrange($redis_lock_key, 0, -1))
            )) {
                throw new BusinessException(ResponseEnum::HTTP_ERROR, '当前时间已被抢先预约，请重新选择时间');
            }
        }

        $parameters['collects']['reservation_date'] = $reservation_date_format;

        $patchwerk->mergeParameters(['collects' => $parameters['collects']]);

        $patchwerk = $next($patchwerk);

        $parameters = $patchwerk->getParameters();

        if (max($parameters['collects']['payer_total'], 0) !== 0 && !in_array($parameters['collects']['pay_channel'], PayChannelEnum::getOffLineChannels())) {
            // 入库前锁定时间
            Redis::connection('order')->rpush($redis_lock_key, json_encode([
                'start' => $parameters['collects']['reservation_time_start'],
                'end' => $parameters['collects']['reservation_time_end'],
            ], JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }

        return $patchwerk;
    }
}
