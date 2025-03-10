<?php

namespace App\Http\Resources\Wechat;

use App\Enums\OrderStatusEnum;
use App\Enums\PayChannelEnum;
use App\Http\Resources\CommentsResource;

class ClientUserOrderResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'no' => $this->trade_no,
                    'cover' => $this->spu_json['images'][0]['url'] ?? null,
                    'trademark_title' => $this->trademark->title,
                    'product_title' => $this->spu_json['title'],
                    'payer_total' => applyIntegerToFloatModifier($this->payer_total),
                    'has_refund' => in_array($this->status, OrderStatusEnum::getRefundStatuses()),
                    'can_cancel' => $this->status === OrderStatusEnum::paying->value,
                    'can_pay' => $this->status === OrderStatusEnum::paying->value,
                    'can_refund' => in_array($this->status, OrderStatusEnum::getFinishStatuses()),
                    'status' => OrderStatusEnum::from($this->status)->name()
                ],
                'show' => [
                    'id' => $this->id,
                    'order_status' => $this->status,
                    'no' => $this->trade_no,
                    'cover' => $this->spu_json['images'][0]['url'] ?? null,
                    'trademark_title' => $this->trademark->title,
                    'product_title' => $this->spu_json['title'],
                    'product_sub_title' => $this->spu_json['sub_title'],
                    'address' => $this->address_json['full_address'],
                    'total' => applyIntegerToFloatModifier($this->total),
                    'payer_total' => applyIntegerToFloatModifier($this->payer_total),
                    'coupon_total' => applyIntegerToFloatModifier($this->coupon_total),
                    'reservation_date' => $this->reservation_date,
                    'reservation_time_start' => $this->reservation_time_start,
                    'reservation_time_end' => $this->reservation_time_end,
                    'pet_avatar' => $this->pet_json['avatar'][0]['url'] ?? null,
                    'pet_name' => $this->pet_json['name'],
                    'pay_channel' => PayChannelEnum::from($this->pay_channel)->name(),
                    'pay_success_at' => $this->pay_success_at,
                    'refund' => (new ClientUserOrderRefundResource($this->refund))->additional(['format' => $format])
                ],
                'default' => []
            },
            false => [
                'id' => $this->id,
                'title' => $this->title
            ]
        };
    }
}
