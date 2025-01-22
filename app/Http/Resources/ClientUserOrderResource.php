<?php

namespace App\Http\Resources;

use App\Enums\OrderStatusEnum;

class ClientUserOrderResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        $result = match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'trade_no' => $this->trade_no,
                    'total' => applyIntegerToFloatModifier($this->total),
                    'payer_total' => applyIntegerToFloatModifier($this->payer_total),
                    'status' => OrderStatusEnum::from($this->status)->name(),
                    'status_color' => $this->transformStatusColor($this->status),
                    'coupon_total' => applyIntegerToFloatModifier($this->coupon_total),
                    'spu_title' => $this->spu_json['title'],
                    'address' => $this->address_json['full_address'],
                    'reservation_date' => $this->reservation_date,
                    'reservation_car' => $this->reservation_car,
                    'reservation_time_start' => $this->reservation_time_start,
                    'reservation_time_end' => $this->reservation_time_end,
                    'pay_channel' => $this->transformPayChannel($this->pay_channel),
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'sub_title' => $this->sub_title,
                    'category_id' => $this->category_id,
                    'trademark_id' => $this->trademark_id,
                    'can_use_pet' => $this->id,
                    'sales_count' => 0,
                    'saleable' => $this->saleable,
                    'description' => $this->description,
                    'images' => $this->images,
                    'packing_list' => $this->packing_list,
                    'after_service' => $this->after_service,
                    'pet_breeds' => $this->spu_breed->pluck('id'),
                ],
                'default' => []
            },
            false => [
                'value' => $this->id,
                'label' => $this->title
            ]
        };

        return $result;
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