<?php

namespace App\Http\Resources\Wechat;

use App\Enums\OrderStatusEnum;
use App\Http\Resources\CommentsResource;

class ClientUserOrderResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        /**
         * "id" => 3
         * "user_id" => 1
         * "goods_id" => 193
         * "sku_id" => 27359021678
         * "address_id" => 1
         * "service_time_id" => 88
         * "pet_id" => 2
         * "coupon_id" => 2
         * "total" => 22000
         * "real_total" => 22000
         * "coupon_total" => 0
         * "trade_no" => "20241129173284652300600011684549"
         * "status" => 0
         * "pay_channel" => 1
         * "remark" => null
         * "created_at" => "2024-11-29 10:15:23"
         * "updated_at" => "2024-11-29 10:15:23"
         * "deleted_at" => null
         */

//        dump($this->id, $this->status, OrderStatusEnum::getRefundStatuses(), in_array($this->status, OrderStatusEnum::getRefundStatuses()));

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'cover' => 'https://crm.misswhite.com.cn/storage/topic/6459cc63daedf.jpg',
                    'trademark_title' => $this->trademark->title,
                    'product_title' => $this->spu->title,
                    'real_total' => applyIntegerToFloatModifier($this->real_total),
                    'has_refund' => in_array($this->status, OrderStatusEnum::getRefundStatuses()),
                    'can_cancel' => $this->status === OrderStatusEnum::paying->value,
                    'can_refund' => in_array($this->status, OrderStatusEnum::getFinishStatuses()),
                    'status' => OrderStatusEnum::from($this->status)->name()
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'permissions' => $this->permissions->pluck('id'),
                    'menus' => $this->menus->pluck('id')
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
