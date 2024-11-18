<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\UserCouponCollection;
use Illuminate\Http\Request;

class UserCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payload = [
            ['id' => 1, 'title' => '5元', 'range' => '全平台', 'description' => '全平台优惠卷，满100立减5元', 'min_price' => 10000, 'amount' => 500, 'expiration_time' => '2024-11-16 20:00:00', 'expiration_timestamp' => strtotime('2024-11-16 20:00:00')],
            ['id' => 2, 'title' => '6元', 'range' => '全平台', 'description' => '全平台优惠卷，满100立减5元', 'min_price' => 10000, 'amount' => 600, 'expiration_time' => '2024-11-18 20:00:00', 'expiration_timestamp' => strtotime('2024-11-18 20:00:00')],
            ['id' => 3, 'title' => '7元', 'range' => '全平台', 'description' => '全平台优惠卷，满100立减5元', 'min_price' => 10000, 'amount' => 700, 'expiration_time' => '2024-11-19 20:00:00', 'expiration_timestamp' => strtotime('2024-11-19 20:00:00')],
            ['id' => 4, 'title' => '8元', 'range' => '全平台', 'description' => '全平台优惠卷，满100立减5元', 'min_price' => 10000, 'amount' => 800, 'expiration_time' => '2024-11-20 20:00:00', 'expiration_timestamp' => strtotime('2024-11-20 20:00:00')],
            ['id' => 5, 'title' => '9元', 'range' => '全平台', 'description' => '全平台优惠卷，满100立减5元', 'min_price' => 10000, 'amount' => 900, 'expiration_time' => '2024-11-21 20:00:00', 'expiration_timestamp' => strtotime('2024-11-21 20:00:00')],
            ['id' => 6, 'title' => '免单', 'range' => '全平台', 'description' => '全平台优惠卷，满100立减5元', 'min_price' => 100000, 'amount' => -1, 'expiration_time' => '2024-11-22 20:00:00', 'expiration_timestamp' => strtotime('2024-11-22 20:00:00')]
        ];

        return $this->success(arrLineToHump($payload));

        return new UserCouponCollection($payload);
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
