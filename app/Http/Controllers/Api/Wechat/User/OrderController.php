<?php

namespace App\Http\Controllers\Api\Wechat\User;

use App\Enums\OrderStatusEnum;
use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\ClientUserOrderResource;
use App\Models\ClientUserAddress;
use App\Models\ClientUserCoupon;
use App\Models\ClientUserOrder;
use App\Models\ClientUserOrderRefund;
use App\Models\ClientUserPet;
use App\Models\ProductSku;
use App\Models\ProductSpu;
use App\Services\OrderService;
use App\Services\TradeDateService;
use App\Services\Wechat\MiniProgramPaymentService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    public function total()
    {
        $orderTotal = ClientUserOrder::withoutGlobalScopes()->selectRaw('COUNT(id) as count, status')
            ->owner()->groupBy('status')->get()->toArray();

        foreach (OrderStatusEnum::cases() as $case) {
            // 输出枚举值名称和对应的中文名称
//            echo "枚举值: {$case->name}, 数值: {$case->value}, 中文名称: {$case->name()}" . PHP_EOL;
            $result[$case->value] = 0;

            foreach ($orderTotal as $item) {
                if ($item['status'] === $case->value) {
                    $result[$case->value] = $item['count'];
                    break; // 找到后立即退出循环
                }
            }
        }


        return $this->success(arrLineToHump($result));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        if (!isset($validated['status'])) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '无效的查询');
        }

        $query = ClientUserOrder::owner();

        if ('all' !== $request->get('status')) {
            $query = $query->where('status', intval($request->get('status')));
        }

        $payload = $query->orderBy('created_at', 'desc')
            ->with('trademark')
            ->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page); // 必须分页

        return $this->success($this->returnIndex($payload, 'Wechat\ClientUserOrderResource', __FUNCTION__));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TradeDateService $dateService, OrderService $orderService)
    {
        $validated = arrHumpToLine($request->input());

        // 解构数据
        [
            'order_spu_info' => $order_spu_info,
            'order_sku_info' => $order_sku_info,
            'order_address_info' => $order_address_info,
            'order_pet_info' => $order_pet_info,
            'order_time_info' => $order_time_info,
            'order_coupon_info' => $order_coupon_info,
            'pay_channel' => $pay_channel,
            'order_remark' => $order_remark,
        ] = $validated;

        $user_id = Auth::guard('wechat')->user()->id;

        // 再次校验预约时间是否被锁定
        $reservation_date = CarbonImmutable::createFromTimeStamp(intval($order_time_info['reservation_date']['time'] / 1000), config('app.timezone'))->format('Y-m-d');

        if (0 !== Redis::connection('order')->exists('reservation_date_' . $reservation_date . '-' . $order_time_info['car_number'])) {
            if (!$dateService->checkTimeRange(
                ['start' => $order_time_info['start_time'], 'end' => $order_time_info['end_time']],
                array_map(fn($item) => json_decode($item, true), Redis::connection('order')->lrange('reservation_date_' . $reservation_date . '-' . $order_time_info['car_number'], 0, -1))
            )) {
                throw new BusinessException(ResponseEnum::HTTP_ERROR, '当前时间已被抢先预约，请重新选择时间');
            }
        }

        // 校验数据合法性
        try {
            $spu = ProductSpu::findOrFail($order_spu_info['id']);
            $sku = ProductSku::where('spu_id', $order_spu_info['id'])->findOrFail($order_sku_info['id']);
            $address = ClientUserAddress::where('user_id', $user_id)->findOrFail($order_address_info['id']);
            $pet = ClientUserPet::where('user_id', $user_id)->findOrFail($order_pet_info['id']);

            $payer_total = $sku->price;

            if (!empty($order_coupon_info)) {
                $coupon = ClientUserCoupon::where('user_id', $user_id)
                    ->where('status', true)
                    ->where('code', $order_coupon_info['code'] ?? null)
                    ->where('min_total', '<=', $sku->price)
                    ->findOrFail($order_coupon_info['id']);

                $payer_total = intval(bcsub($sku->price, $coupon->amount, 0));

                $coupon->status = false;
                $coupon->save();
            }
        } catch (ModelNotFoundException) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '订单创建非法');
        }

        // 入库前锁定时间
        Redis::connection('order')->rpush('reservation_date_' . $reservation_date . '-' . $order_time_info['car_number'], json_encode([
            'reservation_time_start' => $order_time_info['start_time'],
            'reservation_time_end' => $order_time_info['end_time'],
        ], JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $out_trade_no = $orderService->create([
            'total' => $sku->price,
            'payer_total' => max($payer_total, 0),
            'spu_id' => $order_spu_info['id'],
            'spu_json' => $spu->toArray(),
            'category_id' => $spu->category_id,
            'category_title' => $spu->category_id,
            'trademark_id' => $spu->trademark_id,
            'trademark_title' => $spu->trademark_id,
            'sku_id' => $sku->id,
            'sku_json' => $sku->toArray(),
            'address_id' => $address->id,
            'address_json' => $address->toArray(),
            'pet_id' => $pet->id,
            'pet_json' => $pet->toArray(),
            'remark' => $order_remark,
            'pay_channel' => 1,
            'pay_method' => 1,
            'reservation_date' => $reservation_date,
            'reservation_car' => $order_time_info['car_number'],
            'reservation_time_start' => $order_time_info['start_time'],
            'reservation_time_end' => $order_time_info['end_time'],
            'is_revise_price' => false,
            'coupon_id' => isset($coupon) ? $coupon->id : null,
            'coupon_json' => isset($coupon) ? $coupon->toArray() : null,
            'coupon_total' => isset($coupon) ? $coupon->amount : 0,
            'pay_success_at' => max($payer_total, 0) === 0 ? CarbonImmutable::now(config('app.timezone')) : null,
        ], $user_id, max($payer_total, 0) === 0 ? OrderStatusEnum::finishing : OrderStatusEnum::paying);

        $payload = max($payer_total, 0) === 0 ? null : $this->payTransactionsWithChannel('1', $out_trade_no, $payer_total, Auth::guard('wechat')->user()->info->openid, "移动洗护服务-{$order_pet_info['name']}({$order_pet_info['weight']}KG)");

        return $this->success($payload);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payload = ClientUserOrder::owner()->where('trade_no', $id)->with('refund', 'car')->firstOrFail();

        return $this->success((new ClientUserOrderResource($payload))->additional(['format' => __FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->input());

        $order = ClientUserOrder::owner()->where('trade_no', $id)->firstOrFail();

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

        if ($validated['state'] === 'paying') {
            $now = Carbon::now();
            $out_trade_no = date('Ymd') . $now->getPreciseTimestamp(3) . str_pad(1, 4, '0', STR_PAD_LEFT) . random_int(100000, 999999);

            $out_trade_no .= generateLuhnCheckDigit($out_trade_no);

            $order->trade_no = $out_trade_no;

            $payload = $this->payTransactionsWithChannel($validated['pay_channel'], $out_trade_no, $order->payer_total, Auth::guard('wechat')->user()->info->openid, "移动洗护服务-{$order->pet_json['name']}({$order->pet_json['weight']}KG)");
        }

        if ($validated['state'] === 'refund') {
            $now = Carbon::now();
            $out_refund_no = date('Ymd') . $now->getPreciseTimestamp(3) . str_pad(1, 4, '1', STR_PAD_LEFT) . random_int(100000, 999999);

            $out_refund_no .= generateLuhnCheckDigit($out_refund_no);

            ClientUserOrderRefund::create([
                'order_id' => $order->id,
                'refund_no' => $out_refund_no,
                'rationale' => $validated['rationale'],
                'status' => 0,
            ]);
        }

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

    private function payTransactionsWithChannel($payChannel, $outTradeNo, $total, $payerId, $description = '')
    {
        switch ($payChannel) {
            case 1: // 微信支付
                return (new MiniProgramPaymentService())->requestPayment($outTradeNo, $total, $payerId, $description);
        }
    }
}
