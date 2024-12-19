<?php

namespace App\Http\Controllers\Wechat;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\ClientUserOrder;
use App\Services\GoodsSkuService;
use App\Services\UserOrderService;
use App\Services\Wechat\MiniProgramPaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    public function __construct(UserOrderService $service)
    {
        $this->service = $service;
    }

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
        if (!$request->has('status')) return $this->failed('无效的查询');

        $query = ClientUserOrder::select()
//            ->where('user_id', Auth::guard('wechat')->user()->id)
            ->where('user_id', 1);

        if ('all' !== $request->get('status')) {
            $query = $query->where('status', intval($request->get('status')));
        }

        $payload = $query->orderBy('created_at', 'desc')
            ->with(['spu', 'trademark'])
            ->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page); // 必须分页

//        dd($payload->toArray());

        return $this->returnIndex($payload, 'Wechat\ClientUserOrderResource', __FUNCTION__);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MiniProgramPaymentService $paymentService)
    {
        ['orderGoodInfo' => $orderGoodInfo, 'orderAddressInfo' => $orderAddressInfo, 'orderTimeInfo' => $orderTimeInfo, 'orderPetInfo' => $orderPetInfo, 'orderCouponInfo' => $orderCouponInfo] = $request->post();

        $spu_id = $orderGoodInfo['id'];

        try {
            $sku_conditions = ['spu_id' => $spu_id, 'spec_group_id' => $orderGoodInfo['specGroups'][0]['id'], 'spec_values->breed_id' => $orderPetInfo['breedId']];
            if (!in_array($orderPetInfo['weightId'], ['null', 'undefined'])) $sku_conditions['spec_values->weight_id'] = $orderPetInfo['weightId'];

            $sku = (new GoodsSkuService)->find($sku_conditions, ['enable' => true], fields: ['id', 'price']);

            [$s1, $s2] = explode(' ', microtime());
            $microtime = sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
            $out_trade_no = date('Ymd') . $microtime . str_pad(1, 4, '0', STR_PAD_LEFT) . random_int(100000, 999999);

            $checkDigit = generateLuhnCheckDigit($out_trade_no);

            $out_trade_no = $out_trade_no . $checkDigit;

            $payload = $paymentService->requestPayment($out_trade_no, $sku->price, Auth::guard('wechat')->user()->fresh()->loginInfo[0]->wechat_openid, "汪星人宠物服务-{$orderGoodInfo['title']}-{$orderPetInfo['breedTitle']}");

            $this->service->create([
                'user_id' => Auth::guard('wechat')->user()->id,
                'goods_id' => $spu_id,
                'sku_id' => $sku->id,
                'address_id' => $orderAddressInfo['id'],
                'service_time_id' => $orderTimeInfo['id'],
                'pet_id' => $orderPetInfo['id'],
                'coupon_id' => $orderCouponInfo['id'] ?? null,
                'total' => $sku->price,
                'real_total' => $sku->price,
                'coupon_total' => 0,
                'trade_no' => $out_trade_no,
                'status' => 0
            ]);

            return $this->success($payload);

        } catch (ModelNotFoundException $e) {
            dd($e);
            return $this->success([]);
        }
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
