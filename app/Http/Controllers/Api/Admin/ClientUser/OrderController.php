<?php

namespace App\Http\Controllers\Api\Admin\ClientUser;

use App\Http\Controllers\Controller;
use App\Models\ClientUserOrder;
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
        /**
         * $relations = ['spu'];
         *
         * $fields = ['id', 'trade_no', 'total', 'payer_total', 'coupon_total', 'created_at', 'status', 'pay_channel', 'goods_id'];
         *
         * $payload = $this->service->getList(relations: $relations, fields: $fields, paginate: true);
         *
         * return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\ClientUserOrderResource']);
         */


        $query = ClientUserOrder::when(isset($validated['trade_no']), function ($query) use ($validated) {
            return $query->where('trade_no', $validated['trade_no']);
        })->when(isset($validated['pay_channel']), function ($query) use ($validated) {
            return $query->where('pay_channel', $validated['pay_channel']);
        })->when(isset($validated['status']), function ($query) use ($validated) {
            return $query->where('status', $validated['status']);
        })->when(isset($validated['person_phone_number']), function ($query) use ($validated) {
            return $query->where('address_json->person_phone_number', 'like', '%' . $validated['pay_channel'] . '%');
        })->when(isset($validated['address']), function ($query) use ($validated) {
            return $query->where('address_json->full_address', 'like', '%' . $validated['address'] . '%');
        })->orderBy('created_at','desc');

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'ClientUserOrderResource', __FUNCTION__, $paginate);
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
