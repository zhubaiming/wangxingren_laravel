<?php

namespace App\Http\Controllers\Api\Wechat\User;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\ClientUserCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $status = isset($validate['status']) ? isTrue($validate['status']) : true; // 是否分页

        $query = ClientUserCoupon::owner()
            ->when(isset($validate['status']), function ($when) use ($status) {
                $when->where('status', $status);
            })
            ->when(isset($validate['is_get']), function ($when) use ($validate) {
                $when->where('is_get', isTrue($validate['is_get']));
            })
            ->when(!$paginate, function ($when) {
                $when->where('expiration_at', '>=', Carbon::now());
            });

        $payload = $paginate ? $query->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'Wechat\ClientUserCouponResource', __FUNCTION__, $paginate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        $coupon = ClientUserCoupon::owner()->where('code', $id)->firstOrFail();

        if ($coupon->is_get) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '当前优惠券已领取');
        }

        foreach ($validated as $key => $value) {
            $coupon->{$key} = $value;
        }

        $coupon->save();

        return $this->success();
    }
}
