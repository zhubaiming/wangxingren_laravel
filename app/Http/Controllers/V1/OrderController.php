<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseCollection;
use App\Services\UserOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(UserOrderService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $relations = ['spu'];

        $fields = ['id', 'trade_no', 'total', 'real_total', 'coupon_total', 'created_at', 'status', 'pay_channel', 'goods_id'];

        $payload = $this->service->getList(relations: $relations, fields: $fields, paginate: true);

        return (new BaseCollection($payload))->additional(['resource' => 'App\Http\Resources\OrderResource']);
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
