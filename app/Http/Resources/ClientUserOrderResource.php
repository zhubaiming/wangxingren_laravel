<?php

namespace App\Http\Resources;

use App\Enums\GenderEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PayChannelEnum;
use App\Enums\PetWeightRangeEnum;
use Carbon\Carbon;

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
                    'can_cancel' => $this->status === OrderStatusEnum::paying->value || ($this->status === OrderStatusEnum::finishing->value && in_array($this->pay_channel, PayChannelEnum::getOffLineChannels())),
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
                    'reservation_car' => $this->reservation_car,
                    'reservation_time_start' => $this->reservation_time_start,
                    'reservation_time_end' => $this->reservation_time_end,
                    'reservation_time' => $this->reservation_car . '-' . Carbon::parse($this->reservation_time_start)->format('H:i') . '-' . Carbon::parse($this->reservation_time_end)->format('H:i'),
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
                    'pet_gender' => GenderEnum::from($this->pet_json['gender'])->name('animal'),
                    'pet_weight' => PetWeightRangeEnum::from($this->pet_json['weight'])->name(),
                    'pet_age' => is_null($this->pet_json['birth']) ? 0 : calculateAge($this->pet_json['birth'], 'Y-m'),
                    'pet_is_sterilization' => $this->pet_json['is_sterilization'] ? '是' : '否'
                ],
                'default' => []
            },
            false => [
//                'name' => $this->trade_no . '\n' . $this->spu_json['title'] . '\n支付金额: ¥' . applyIntegerToFloatModifier($this->payer_total) . '\n' . '\n预约时间: ' . $this->reservation_time_start . '\n服务地址: ' . $this->address_json['full_address'],
                'name' => [
                    'trade_no' => $this->trade_no,
                    'spu_title' => $this->spu_json['title'],
                    'reservation_time' => $this->reservation_time_start,
                    'address' => $this->address_json['province'] . $this->address_json['city'] . $this->address_json['district'] . $this->address_json['street']
                ],
                'value' => [
                    $this->reservation_car,
                    Carbon::parse($this->reservation_date . ' ' . $this->reservation_time_start)->valueOf(),
                    Carbon::parse($this->reservation_date . ' ' . $this->reservation_time_end)->valueOf(),
                    Carbon::parse($this->reservation_date . ' ' . $this->reservation_time_start)->diffInMilliseconds(Carbon::parse($this->reservation_date . ' ' . $this->reservation_time_end)),
                ]
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