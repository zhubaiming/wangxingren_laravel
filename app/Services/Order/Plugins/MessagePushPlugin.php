<?php

namespace App\Services\Order\Plugins;

use App\Enums\GenderEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PetWeightRangeEnum;
use App\Events\NewPayedOrderEvent;
use App\Jobs\WecomBotMessageJob;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

class MessagePushPlugin implements PluginInterface
{

    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();
        /*
        * 消息推送
        * 1、推送后台
        * 2、推送企业微信机器人
        */
        if ($parameters['status'] === OrderStatusEnum::finishing) {
            NewPayedOrderEvent::dispatch();
        }

        WecomBotMessageJob::dispatch(
            'markdown',
            "今日日期: " . $parameters['time']->isoFormat('YYYY年MM月DD日') .
            "\n\n\n# 新增预约订单\n\n\n> 订单编号: <font color=\"comment\">" . $parameters['trade_no'] .
            "</font>\n预约时间: **<font color=\"info\">" . $parameters['collects']['reservation_date'] . " - " . $parameters['collects']['reservation_time_start'] .
            "</font>**\n预约项目 **<font color=\"info\">" . $parameters['collects']['spu_json']['title'] .
            "</font>**\n服务地址: <font color=\"warning\">" . $parameters['collects']['address_json']['full_address'] .
            "</font>\n联系电话: <font color=\"info\">+" . $parameters['collects']['address_json']['person_phone_prefix'] . " " . $parameters['collects']['address_json']['person_phone_number'] .
            "</font>\n宠物品种: <font color=\"comment\">" . $parameters['collects']['pet_json']['breed_title'] .
            "</font>\n宠物体重范围: <font color=\"comment\">" . PetWeightRangeEnum::from(applyFloatToIntegerModifier($parameters['collects']['pet_json']['weight']))->name() .
            "</font>\n宠物信息: " . $parameters['collects']['pet_json']['name'] . "(" . GenderEnum::from($parameters['collects']['pet_json']['gender'])->name('animal') . "-" . (is_null($parameters['collects']['pet_json']['birth']) ? 0 : calculateAge($parameters['collects']['pet_json']['birth'], 'Y-m')) . "岁)"
        );

        return $next($patchwerk);
    }
}
