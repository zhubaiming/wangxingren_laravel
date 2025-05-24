<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\CommentsRequest;

class StoreRequest extends CommentsRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
//        dd($this);
        return match (strtok($this->route()->action['prefix'], '/')) {
            default => [],
            'wechat' => [
                'order_spu_info' => ['required', 'array'],
                'order_spu_info.id' => ['required', 'integer', 'gt:0'],
                'order_sku_info' => ['required', 'array'],
                'order_sku_info.id' => ['required', 'integer', 'gt:0'],
                'order_address_info' => ['required', 'array'],
                'order_address_info.id' => ['required', 'integer', 'gt:0'],
                'order_pet_info' => ['required', 'array'],
                'order_pet_info.id' => ['required', 'integer', 'gt:0'],
                'order_time_info' => ['required', 'array'],
                'order_time_info.reservation_date' => ['required', 'array'],
                'order_time_info.reservation_date.time' => ['required', 'integer', 'digits:13', 'gt:' . time()],
                'order_time_info.car_number' => ['required', 'integer', 'gt:0'],
                'order_time_info.start_time' => ['required', 'date_format:H:i'],
                'order_time_info.end_time' => ['required', 'date_format:H:i', 'after:order_time_info.start_time'],
                'order_coupon_info' => ['nullable', 'array'],
                'order_coupon_info.id' => ['required_with:order_coupon_info', 'integer', 'gt:0'],
                'pay_channel' => ['required', 'integer', 'gt:0', 'same:pay_channel', 'string'],
                'order_remark' => ['nullable', 'string'],
            ],
            'api' => [
                'client_user_id' => ['required', 'integer', 'gt:0'],
                'spu_id' => ['required', 'integer', 'gt:0'],
                'sku_id' => ['required', 'integer', 'gt:0'],
                'client_user_address_id' => ['required', 'integer', 'gt:0'],
                'client_user_pet_id' => ['required', 'integer', 'gt:0'],
//                'client_user_coupon_id' => ['required', 'integer', 'gt:0'],
                'client_user_coupon_id' => ['nullable', 'integer', 'gt:0'],
                'pay_channel' => ['required', 'integer', 'gt:0', 'same:pay_channel', 'string'],
                'reservation_date' => ['required', 'regex:/^\d{10}(\d{3})?$/'],
                'reservation_time' => ['required', 'regex:/\d+-\d{2}:\d{2}-\d{2}:\d{2}/'],
                'payer_total' => ['required', 'integer', 'gte:0'],
                'remark' => ['nullable', 'string'],
                'user' => ['required', 'string']
            ]
        };
    }
}
