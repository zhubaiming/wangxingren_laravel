<?php

namespace App\Http\Controllers\Api\Admin\ClientUser;

use App\Http\Controllers\Controller;
use App\Models\ClientUserCoupon;
use App\Models\ProductSku;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());
        $paginate = isset($validated['paginate']) ? isTrue($validated['paginate']) : true; // 是否分页

        $skuPrice = ProductSku::select('price')->find($validated['sku_id'])->price;

        $payload = ClientUserCoupon::where('user_id', $validated['user_id'])->where('status', true)->where('is_get', true)
            ->where(function ($query) use ($skuPrice) {
                $query->where('min_total', '<=', $skuPrice)->orWhere('min_total', 0);
            })
            ->when(!$paginate, function ($query) {
                $query->where(function ($q) {
                    $q->where('expiration_at', '>=', Carbon::now())->orWhereNull('expiration_at');
                });
            });

        $payload = $paginate ? $payload->paginate($validated['page_size'] ?? $this->pageSize, ['*'], 'page', $validated['page'] ?? $this->page) : $payload->get();

        return $this->success($this->returnIndex($payload, 'ClientUserCouponResource', __FUNCTION__, $paginate));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
