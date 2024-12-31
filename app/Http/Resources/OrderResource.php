<?php

namespace App\Http\Resources;

class OrderResource extends CommentsResource
{
    protected function resourceData(): array
    {
        return [
            'id' => $this->id,
            'trade_no' => $this->trade_no,
            'total' => applyIntegerToFloatModifier($this->total),
            'payer_total' => applyIntegerToFloatModifier($this->payer_total),
            'coupon_total' => applyIntegerToFloatModifier($this->coupon_total),
            'created_at' => $this->created_at,
            'status_color' => $this->transformStatusColor($this->status),
            'status' => $this->transformStatus($this->status),
            'goods_title' => $this->spu->title,
            'user_address' => '',
            'service_time' => '',
            'pay_channel' => $this->transformPayChannel($this->pay_channel),
        ];
    }

    private function transformStatus($status)
    {
        switch ($status) {
            case 0:
                return '待付款';
            case 1:
                return '已付款';
            case 2:
                return '已完成';
            case 3:
                return '已取消';
            case 4:
                return '申请退款';
            case 5:
                return '已退款';
            default:
                return '状态错误';
        }
    }

    private function transformStatusColor($status)
    {
        switch ($status) {
            case 0:
                return ['type' => 'info', 'color' => []];
            case 1:
                return ['type' => 'warning', 'color' => []];
            case 2:
                return ['type' => 'success', 'color' => []];
            case 3:
                return ['type' => 'error', 'color' => []];
            case 4:
                return ['type' => '', 'color' => ['color' => '#FFF2E2']];
            case 5:
                return ['type' => '', 'color' => ['color' => '#CCE8CF']];
            default:
                return ['type' => '', 'color' => ['color' => '#EAEAEF']];
        }
    }

    private function transformPayChannel($channel)
    {
        switch ($channel) {
            case 1:
                return '微信支付';
            case 2:
                return '支付宝支付';
            case 3:
                return '会员卡消费';
            case 4:
                return '线下支付';
            default:
                return '未知';
        }
    }
}