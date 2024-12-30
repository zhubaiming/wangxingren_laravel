<?php

namespace App\Http\Controllers\Api\Wechat\User;

use App\Http\Controllers\Controller;
use App\Models\ClientUserCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = ClientUserCoupon::where(['user_id' => Auth::guard('wechat')->user()->id])->where('status', true)
            ->when(!$paginate, function ($when) {
                $when->where('expiration_at', '>=', Carbon::now());
            });

        $payload = $paginate ? $query->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'Wechat\ClientUserCouponResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.Ï
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
