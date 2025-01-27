<?php

namespace App\Http\Resources;

use App\Enums\OrderStatusEnum;
use App\Enums\PayChannelEnum;

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
                    'status' => $this->status,
                    'status_enum' => OrderStatusEnum::from($this->status)->name(),
                    'status_color' => $this->transformStatusColor($this->status),
                    'coupon_total' => applyIntegerToFloatModifier($this->coupon_total),
                    'spu_title' => $this->spu_json['title'],
                    'address' => $this->address_json['full_address'],
                    'reservation_date' => $this->reservation_date,
                    'reservation_car' => $this->reservation_car,
                    'reservation_time_start' => $this->reservation_time_start,
                    'reservation_time_end' => $this->reservation_time_end,
                    'pay_channel' => PayChannelEnum::from($this->pay_channel)->name(),
                ],
                'show' => [
                    'id' => $this->id,
                    'trade_no' => $this->trade_no,
                    'status' => $this->status,
                    'status_enum' => OrderStatusEnum::from($this->status)->name(),
                    'total' => applyIntegerToFloatModifier($this->total),
                    'payer_total' => applyIntegerToFloatModifier($this->payer_total),
                    'coupon_total' => applyIntegerToFloatModifier($this->coupon_total),
                    'pay_channel' => PayChannelEnum::from($this->pay_channel)->name(),
                    'currency' => $this->currency,
                    'reservation_date' => $this->reservation_date,
                    'reservation_time_start' => $this->reservation_time_start,
                    'reservation_time_end' => $this->reservation_time_end,
                    'pay_success_at' => $this->pay_success_at,
                    'remark' => $this->remark,
                    'spu_title' => $this->spu_json['title'],
                    'spu_images' => $this->spu_json['images'],
                    'sku_price' => applyIntegerToFloatModifier($this->sku_json['price']),
                    'sku_duration' => $this->sku_json['duration'],
                    'address' => $this->address_json['full_address'],
                    'address_person_name' => $this->address_json['person_name'],
                    'address_person_phone' => '+' . $this->address_json['person_phone_prefix'] . ' ' . $this->address_json['person_phone_number'],
                    'pet_name' => $this->pet_json['name'],
                    'pet_breed_title' => $this->pet_json['breed_title'],
                    'pet_gender' => $this->pet_json['gender_conv'],
                    'pet_weight' => $this->pet_json['weight'] . '(kg)',
                    'pet_age' => calculateAge($this->pet_json['birth'], 'Y-m'),
                    'pet_is_sterilization' => $this->pet_json['is_sterilization'] ? '是' : '否'
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
                return ['type' => 'error', 'color' => []];
            case 1:
                return ['type' => 'warning', 'color' => []];
            case 2:
                return ['type' => 'success', 'color' => []];
            case 3:
                return ['type' => 'info', 'color' => []];
            case 4:
                return ['type' => '', 'color' => ['color' => '#FFF2E2']];
            case 5:
                return ['type' => '', 'color' => ['color' => '#CCE8CF']];
            default:
                return ['type' => '', 'color' => ['color' => '#EAEAEF']];
        }
    }
}