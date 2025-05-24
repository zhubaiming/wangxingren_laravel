<?php

namespace App\Http\Controllers\Api\Admin\ClientUser;

use App\Enums\GenderEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PayChannelEnum;
use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Resources\ClientUserOrderResource;
use App\Models\ClientUserAddress;
use App\Models\ClientUserCoupon;
use App\Models\ClientUserOrder;
use App\Models\ClientUserPet;
use App\Models\ProductSku;
use App\Models\ProductSpu;
use App\Services\OrderService;
use App\Services\TradeDateService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
            return $query->where('address_json->person_phone_number', 'like', '%' . $validated['person_phone_number'] . '%');
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
    public function store(StoreRequest $request, OrderService $orderService)
    {
        [
            'client_user_id' => $client_user_id,
//            'trademark_id' => $trademark_id,
//            'category_id' => $category_id,
            'spu_id' => $spu_id,
            'sku_id' => $sku_id,
            'client_user_address_id' => $client_user_address_id,
            'client_user_pet_id' => $client_user_pet_id,
            'client_user_coupon_id' => $client_user_coupon_code,
            'reservation_date' => $reservation_date,
            'reservation_time' => $reservation_time,
            'pay_channel' => $pay_channel,
            'remark' => $remark,
            'payer_total' => $payer_total,
//            'duration' => $duration,
            'user' => $user
        ] = $request->safe()->all();

        $reservation = explode('-', $reservation_time);
        if (count($reservation) !== 3) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '预约时间格式非法');
        }
        if (strtotime($reservation[1] >= $reservation[2])) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '结束时间必须大于开始时间');
        }

        $payer_total = applyFloatToIntegerModifier($payer_total);
        $reservation_date = CarbonImmutable::createFromTimeStamp($reservation_date / 1000, config('app.timezone'));

        $orderService->create([
            'payer_total' => $payer_total,
            'spu_id' => $spu_id,
            'sku_id' => $sku_id,
            'address_id' => $client_user_address_id,
            'pet_id' => $client_user_pet_id,
            'remark' => $remark,
            'pay_channel' => $pay_channel,
            'pay_method' => null,
            'reservation_date' => $reservation_date,
            'reservation_car' => $reservation[0],
            'reservation_time_start' => $reservation[1],
            'reservation_time_end' => $reservation[2],
            'is_revise_price' => false,
            'coupon_id' => null,
//            'coupon_code' => $client_user_coupon_code,
            'revise_by' => $user
        ], $client_user_id);

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

        if (isset($validated['state'])) { // 修改订单状态
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
        }

        if (isset($validated['payer_total'])) { // 修改订单价格
            $order->payer_total = applyFloatToIntegerModifier($validated['payer_total']);
            $order->is_revise_price = true;
            $order->revise_by = $validated['user'];
        }

        $order->remark = $validated['remark'] ?? $order->remark;

        if (isset($validated['reservation_time'])) {
            $reservation = explode('-', $validated['reservation_time']);
            if (count($reservation) !== 3) {
                throw new BusinessException(ResponseEnum::HTTP_ERROR, '预约时间格式非法');
            }
            $order->reservation_car = $reservation[0];
            $order->reservation_time_start = $reservation[1];
            $order->reservation_time_end = $reservation[2];
        }

        $order->save();

        return $this->success();
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
        $payload = array_merge(array_map(function ($status) {
            return [
                'label' => $status->name(),
                'value' => $status->value
            ];
        }, OrderStatusEnum::cases()));

        return $this->success($payload);
    }

    public function allPayChannels()
    {
        $payload = array_filter(PayChannelEnum::cases(), function ($channel) {
            return 'unknown' !== $channel->name;
        });

        $payload = array_merge(array_map(function ($channel) {
            return [
                'label' => $channel->name(),
                'value' => $channel->value
            ];
        }, $payload));

        return $this->success($payload);
    }
}
