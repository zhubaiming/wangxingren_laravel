<?php

namespace App\Services;

use App\Enums\GenderEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PetWeightRangeEnum;
use App\Events\NewPayedOrderEvent;
use App\Jobs\DelayCannelOrder;
use App\Models\ClientUserOrder;
use Carbon\CarbonImmutable;
use Zhenzhu\MessagePusher\MessagePush;

class OrderService
{
    private readonly \DateTimeInterface $initTime;

    private readonly \DateTimeInterface $time;

    public function __construct()
    {
        $this->initTime = CarbonImmutable::create(2024, 10, 8, 0, 0, 0, config('app.timezone'));

        $this->time = CarbonImmutable::now(config('app.timezone'));
    }

    /**
     * 生成订单编号
     */
    private function generateOrderNo($user_id, $type)
    {
        // 随机码
        $randomCode = intval($this->time->diffInSeconds($this->initTime, true)) % 1000;

        $count = ClientUserOrder::whereBetween('created_at', [$this->time->startOfDay(), $this->time->endOfDay()])->count('id');
        $sequence = $count + 1;

        $orderSqe = null;
        $randomCodeArr = str_split(str_pad(strval($randomCode), 3, '0', STR_PAD_LEFT));
        foreach ([substr($sequence, 0, -2), substr($sequence, -2, -1), substr($sequence, -1)] as $idx => $val) {
            $orderSqe .= ($val === '' ? null : $val) . $randomCodeArr[$idx];
        }

        $reverseUserId = reverseNumberMath($user_id % 1000);

        $code = '1' . $orderSqe . ($type === 'backend' ? '0' : '1') . str_pad(strval($user_id % 100), 2, '0', STR_PAD_LEFT) . str_pad(strval($reverseUserId), 3, '0', STR_PAD_RIGHT);

        $code .= generateLuhnCheckDigit($code);

        if (strlen($code) < 10) {
            $pow = 10 - strlen($code);
            $code .= random_int((10 ** ($pow - 1)), (10 ** $pow - 1));
        }

        return intval($code);
    }

    /**
     * 生成流水编号
     */
    private function generateTransactionsNo()
    {
        $out_trade_no = $this->time->isoFormat('YYYYMMDD') . $this->time->getPreciseTimestamp(3) . str_pad(1, 4, '0', STR_PAD_LEFT) . random_int(100000, 999999);

        $out_trade_no .= generateLuhnCheckDigit($out_trade_no);

        return $out_trade_no;
    }

    /**
     * 生成外部订单编号
     */
    private function generateOutTradeNo()
    {

    }

    public function create(array $collects, int $user_id, OrderStatusEnum $status = OrderStatusEnum::paying, ?string $type = null)
    {
        $order_no = $this->generateOrderNo($user_id, $type);
        $trade_no = $this->generateTransactionsNo();

        $collects = [
            ...$collects,
            'order_no' => $order_no,
            'trade_no' => $trade_no,
            'user_id' => $user_id,
            'status' => $status,
            'expected_at' => $this->time->addSeconds(300)->toDateTimeString()
        ];

        // 创建订单
        ClientUserOrder::create($collects, $type);

        // 创建流水

        if ($status === OrderStatusEnum::paying) {
            // 订单流入队列，进行15分钟未支付取消的判断
            DelayCannelOrder::dispatch($order_no)->delay($this->time->addSeconds(300));
        }

        /*
         * 消息推送
         * 1、推送后台
         * 2、推送企业微信机器人
         */
        NewPayedOrderEvent::dispatch();

        (new MessagePush())->channel('wecom')
            ->delivery('groupBot')
            ->sendMarkdown(
                'e893bd4e-9e6f-445c-8f6e-dcb9f99f6d4c',
                "今日日期: " . $this->time->isoFormat('YYYY年MM月DD日') . "\n\n\n# 新增预约订单\n\n\n> 订单编号: <font color=\"comment\">{$trade_no}</font>\n预约时间: **<font color=\"info\">xx:xx</font>**\n服务地址: <font color=\"warning\">" . $collects['address_json']['full_address'] . "</font>\n宠物品种: <font color=\"comment\">" . $collects['pet_json']['breed_title'] . "</font>\n宠物体重范围: <font color=\"comment\">" . PetWeightRangeEnum::from(applyFloatToIntegerModifier($collects['pet_json']['weight']))->name() . "</font>\n宠物信息: " . $collects['pet_json']['name'] . "(" . GenderEnum::from($collects['pet_json']['gender'])->name('animal') . "-" . is_null($collects['pet_json']['birth']) ? 0 : calculateAge($collects['pet_json']['birth'], 'Y-m') . "岁)"
            );
//        new MessagePush()->channel('wecom')->delivery('groupBot')->sendMarkdown('e893bd4e-9e6f-445c-8f6e-dcb9f99f6d4c', "今日日期: {$this->time->isoFormat('YYYYMMDD')}xxxx年xx月xx日\n今日实时预约订单: **<font color=\"warning\">xxxxxx笔</font>**，请相关同事注意。\n\n\n# 新增预约订单\n\n\n> 订单编号: <font color=\"comment\">{$trade_no}</font>\n预约时间: **<font color=\"info\">xx:xx</font>**\n服务地址: <font color=\"warning\">{$collects['address_json']['full_address']}</font>\n宠物品种: <font color=\"comment\">{$collects['pet_json']['breed_title']}</font>\n宠物体重范围: <font color=\"comment\">" . PetWeightRangeEnum::from(applyFloatToIntegerModifier($collects['pet_json']['weight']))->name() . "</font>\n宠物信息: {$collects['pet_json']['name']}(" . GenderEnum::from($collects['pet_json']['gender'])->name('animal') . "-" . is_null($collects['pet_json']['birth']) ? 0 : calculateAge($collects['pet_json']['birth'], 'Y-m') . "岁)");

        return $trade_no;
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