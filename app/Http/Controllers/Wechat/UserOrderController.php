<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Pivot\SysPivotGoodsSkuValue;
use App\Models\SysGoodsSku;
use App\Services\Wechat\MiniProgramPaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MiniProgramPaymentService $paymentService)
    {
//        dd($request->post());
//        dd(random_int(1000000000, 9999999999), random_bytes(10));

        list('orderGoodInfo' => $orderGoodInfo, 'orderAddressInfo' => $orderAddressInfo, 'orderTimeInfo' => $orderTimeInfo, 'orderPetInfo' => $orderPetInfo, 'orderCouponInfo' => $orderCouponInfo) = $request->post();

        $spu_id = $orderGoodInfo['id'];

        try {
            $sku_id = SysPivotGoodsSkuValue::where(['spu_id' => $spu_id, 'spec_group_id' => $orderGoodInfo['specGroups'][0]['id'], 'spec_value_id' => $orderPetInfo['breedId']])->value('sku_id');

//            dump($sku_id);

//            $sku = SysGoodsSku::where(['id' => $sku_id])->firstOrFail();
            $sku = SysGoodsSku::findOrFail($sku_id);

//            dd($sku);

            list($s1, $s2) = explode(' ', microtime());
            $microtime = sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
            $out_trade_no = date('Ymd') . $microtime . str_pad(1, 4, '0', STR_PAD_LEFT) . random_int(10000000, 99999999);

            $checkDigit = generateLuhnCheckDigit($out_trade_no);

            $out_trade_no = $out_trade_no . $checkDigit;

//            dd($out_trade_no);

            //  return response()->json($service->requestPayment('a', 1, 'b'));
            $payload = $paymentService->requestPayment($out_trade_no, $sku->price, Auth::guard('wechat')->user()->fresh()->loginInfo[0]->wechat_openid, "汪星人宠物服务-{$orderGoodInfo['title']}-{$orderPetInfo['breedTitle']}");

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
