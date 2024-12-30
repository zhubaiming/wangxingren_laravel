<?php

namespace App\Http\Controllers\Api\Wechat\User;

use App\Enums\OrderStatusEnum;
use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\ClientUserAddress;
use App\Models\ClientUserCoupon;
use App\Models\ClientUserOrder;
use App\Models\ClientUserPet;
use App\Models\ProductSku;
use App\Models\ProductSpu;
use App\Services\Wechat\MiniProgramPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function total()
    {
        $orderTotal = ClientUserOrder::withoutGlobalScopes()->selectRaw('COUNT(id) as count, status')
            ->where('user_id', Auth::guard('wechat')->user()->id)
            ->groupBy('status')
            ->get()->toArray();

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


        return $this->success($result);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        if (!isset($validate['status'])) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '无效的查询');
        }

        $query = ClientUserOrder::where('user_id', Auth::guard('wechat')->user()->id);

        if ('all' !== $request->get('status')) {
            $query = $query->where('status', intval($request->get('status')));
        }

        $payload = $query->orderBy('created_at', 'desc')
            ->with(['spu', 'trademark'])
            ->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page); // 必须分页

        return $this->returnIndex($payload, 'Wechat\ClientUserOrderResource', __FUNCTION__);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MiniProgramPaymentService $paymentService)
    {
        $validate = arrHumpToLine($request->input());
//        dd($validate);

        [
            'order_spu_info' => $order_spu_info,
            'order_sku_info' => $order_sku_info,
            'order_address_info' => $order_address_info,
            'order_pet_info' => $order_pet_info,
            'order_time_info' => $order_time_info,
            'order_coupon_info' => $order_coupon_info,
            'pay_channel' => $pay_channel,
            'order_remark' => $order_remark,
        ] = $validate;

        if (
            0 === ProductSpu::where('id', $order_spu_info['id'])->count('id') ||
            0 === ProductSku::where('id', $order_sku_info['id'])->count('id') ||
            0 === ClientUserAddress::where('id', $order_address_info['id'])->where('user_id', Auth::guard('wechat')->user()->id)->count('id') ||
            0 === ClientUserPet::where('id', $order_pet_info['id'])->where('user_id', Auth::guard('wechat')->user()->id)->count('id')

        ) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '订单创建非法');
        }

        $now = Carbon::now();
        $out_trade_no = date('Ymd') . $now->getPreciseTimestamp(3) . str_pad(1, 4, '0', STR_PAD_LEFT) . random_int(100000, 999999);

        $out_trade_no .= generateLuhnCheckDigit($out_trade_no);

//        dd($out_trade_no, $checkDigit);

        $order = [
            'trade_no' => $out_trade_no,
            'status' => OrderStatusEnum::paying,
            'user_id' => Auth::guard('wechat')->user()->id,
            'total' => $order_sku_info['price'],
            'real_total' => $order_sku_info['price'],
            'spu_id' => $order_spu_info['id'],
            'spu_json' => json_encode($order_spu_info, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'category_id' => $order_spu_info['category_id'],
            'category_title' => $order_spu_info['category_id'],
            'trademark_id' => $order_spu_info['trademark_id'],
            'trademark_title' => $order_spu_info['trademark_id'],
            'sku_id' => $order_sku_info['id'],
            'sku_json' => json_encode($order_sku_info, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'address_id' => $order_address_info['id'],
            'address_json' => json_encode($order_address_info, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'pet_id' => $order_pet_info['id'],
            'pet_json' => json_encode($order_pet_info, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'remark' => $order_remark,
            'pay_channel' => $pay_channel,
            'reservation_date' => $order_time_info['reservation_date'],
            'reservation_car' => $order_time_info['car_number'] . ' 号车',
            'reservation_time_start' => $order_time_info['start_time'],
            'reservation_time_end' => $order_time_info['end_time'],
            'expected_at' => $now->addMinutes(15)->toDateTimeString()
        ];

        if (!empty($order_coupon_info)) {
            if (0 === ClientUserCoupon::where('id', $order_coupon_info['id'])->where('user_id', Auth::guard('wechat')->user()->id)->where('status', true)->count('id')) {
                throw new BusinessException(ResponseEnum::HTTP_ERROR, '订单创建非法');
            }
            $real_total = intval(bcsub($order['total'], $order_coupon_info['amount'], 0));
            $order = array_merge($order, [
                'coupon_id' => $order_coupon_info['id'],
                'coupon_json' => json_encode($order_coupon_info, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'real_total' => $real_total < 0 ? 0 : $real_total,
                'coupon_total' => $order_coupon_info['amount']
            ]);

            if (0 === $order['real_total']) {
                $order['status'] = OrderStatusEnum::finished;
            }
        }

        ClientUserOrder::create($order);

        $payload = null;
        if (0 !== $order['real_total']) {
            switch ($pay_channel) {
                case 1: // 微信支付
                    $payload = (new MiniProgramPaymentService())->requestPayment(
                        $out_trade_no,
                        $order['real_total'],
                        Auth::guard('wechat')->user()->fresh()->loginInfo[0]->wechat_openid,
                        "移动洗护服务-{$order_spu_info['title']}-{$order_pet_info['name']}({$order_pet_info['weight']}KG)"
                    );
                    break;
            }
        }

        return $this->success($payload);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
