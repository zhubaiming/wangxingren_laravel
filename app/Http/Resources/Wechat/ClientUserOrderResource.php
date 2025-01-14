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

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'cover' => 'https://crm.misswhite.com.cn/storage/topic/6459cc63daedf.jpg',
                    'trademark_title' => $this->trademark->title,
                    'product_title' => $this->spu->title,
                    'payer_total' => applyIntegerToFloatModifier($this->payer_total),
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
