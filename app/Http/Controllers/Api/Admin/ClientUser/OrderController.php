<?php

namespace App\Http\Controllers\Api\Admin\ClientUser;

use App\Enums\GenderEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PayChannelEnum;
use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientUserOrderResource;
use App\Models\ClientUserAddress;
use App\Models\ClientUserCoupon;
use App\Models\ClientUserOrder;
use App\Models\ClientUserPet;
use App\Models\ProductSku;
use App\Models\ProductSpu;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $payload = ClientUserOrder::when(isset($validated['trade_no']), function ($query) use ($validated) {
            return $query->where('trade_no', $validated['trade_no']);
        })->when(isset($validated['pay_channel']), function ($query) use ($validated) {
            return $query->where('pay_channel', $validated['pay_channel']);
        })->when(isset($validated['order_status']), function ($query) use ($validated) {
            return $query->whereIn('status', explode(',', $validated['order_status']));
        })->when(isset($validated['person_phone_number']), function ($query) use ($validated) {
            return $query->where('address_json->person_phone_number', 'like', '%' . $validated['pay_channel'] . '%');
        })->when(isset($validated['address']), function ($query) use ($validated) {
            return $query->where('address_json->full_address', 'like', '%' . $validated['address'] . '%');
        })->when(isset($validated['reservation_date']), function ($query) use ($validated) {
            return $query->where('reservation_date', Carbon::createFromTimeStamp($validated['reservation_date'] / 1000, config('app.timezone'))->format('Y-m-d'));
        })->orderBy('created_at', 'desc');

        $payload = $paginate ? $payload->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page) : $payload->get();

        return $this->success($this->returnIndex($payload, 'ClientUserOrderResource', __FUNCTION__, $paginate));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        [
            'client_user_id' => $client_user_id,
            'trademark_id' => $trademark_id,
            'category_id' => $category_id,
            'spu_id' => $spu_id,
            'sku_id' => $sku_id,
            'client_user_address_id' => $client_user_address_id,
            'client_user_pet_id' => $client_user_pet_id,
            'client_user_coupon_id' => $client_user_coupon_code,
            'reservation_date' => $reservation_date,
            'reservation_time' => $reservation_time,
            'pay_channel' => $pay_channel,
            'remark' => $remark,
        ] = $validated;

        try {
            $spu = ProductSpu::where('trademark_id', $trademark_id)->where('category_id', $category_id)->findOrFail($spu_id);
            $sku = ProductSku::where('spu_id', $spu_id)->findOrFail($sku_id);
            $address = ClientUserAddress::where('user_id', $client_user_id)->findOrFail($client_user_address_id);
            $pet = ClientUserPet::where('user_id', $client_user_id)->findOrFail($client_user_pet_id);
        } catch (ModelNotFoundException) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '订单创建非法');
        }

        $reservation_date = Carbon::createFromTimeStamp($reservation_date / 1000, config('app.timezone'));

        $now = Carbon::now();

        if ($reservation_date->lt(Carbon::today())) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '预约日期非法');
        }

        $reservation = explode('-', $reservation_time);
        if (count($reservation) !== 3) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '预约时间格式非法');
        }

        $out_trade_no = date('Ymd') . $now->getPreciseTimestamp(3) . str_pad(1, 4, '0', STR_PAD_LEFT) . random_int(100000, 999999);

        $out_trade_no .= generateLuhnCheckDigit($out_trade_no);

        $pet->gender_conv = GenderEnum::from($pet->gender)->name('animal');

        $order = [
            'trade_no' => $out_trade_no,
            'user_id' => $client_user_id,
            'status' => in_array($pay_channel, PayChannelEnum::getOffLineChannels()) ? OrderStatusEnum::finishing : OrderStatusEnum::paying,
            'total' => $sku->price,
            'payer_total' => $sku->price,
            'spu_id' => $spu_id,
            'spu_json' => $spu->toArray(),
            'category_id' => $category_id,
            'category_title' => $category_id,
            'trademark_id' => $trademark_id,
            'trademark_title' => $trademark_id,
            'sku_id' => $sku_id,
            'sku_json' => $sku->toArray(),
            'address_id' => $client_user_address_id,
            'address_json' => $address->toArray(),
            'pet_id' => $client_user_pet_id,
            'pet_json' => $pet->makeHidden('deleted_at')->toArray(),
            'remark' => $remark,
            'pay_channel' => $pay_channel,
            'reservation_date' => $reservation_date->format('Y-m-d'),
            'reservation_car' => $reservation[0],
            'reservation_time_start' => $reservation[1],
            'reservation_time_end' => $reservation[2],
            'expected_at' => $now->addMinutes(15)->toDateTimeString()
        ];

        if ($coupon = ClientUserCoupon::where('user_id', $client_user_id)->where('status', true)->where('code', $client_user_coupon_code)->where('is_get', true)->first()) {
            $payer_total = intval(bcsub($sku->price, $coupon->amount, 0));
            $order = array_merge($order, [
                'coupon_id' => $coupon->id,
                'coupon_json' => $coupon->toArray(),
                'payer_total' => max($payer_total, 0),
                'coupon_total' => $coupon->amount
            ]);

            if (0 === $order['payer_total']) {
                $order['status'] = OrderStatusEnum::finishing;
            }

            $coupon->status = false;
            $coupon->save();
        }

        ClientUserOrder::create($order);

        return $this->success();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = ClientUserOrder::findOrFail($id);

        return $this->success((new ClientUserOrderResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->input());

        $order = ClientUserOrder::where('trade_no', $id)->firstOrFail();

        $orderState = null;
        foreach (OrderStatusEnum::cases() as $case) {
            // 输出枚举值名称和对应的中文名称
//            echo "枚举值: {$case->name}, 数值: {$case->value}, 中文名称: {$case->name()}" . PHP_EOL;
            if ($case->name === $validated['state']) {
                $orderState = $case->value;
            }
        }

        if (is_null($orderState)) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '当前操作无效');
        }

        $order->status = $orderState;

        $payload = null;

        $order->save();

        return $this->success($payload);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function allStatus()
    {
        $payload = array_map(function ($status) {
            return [
                'label' => $status->name(),
                'value' => $status->value
            ];
        }, OrderStatusEnum::cases());

        return $this->success($payload);
    }
}
