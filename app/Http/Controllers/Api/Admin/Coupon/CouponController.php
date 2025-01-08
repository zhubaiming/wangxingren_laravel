<?php

namespace App\Http\Controllers\Api\Admin\Coupon;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\ClientUserCoupon;
use App\Models\Coupon;
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

        $query = Coupon::orderBy('id', 'asc');

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'CouponResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->post());

        if (0 === Coupon::where('title', $validated['title'])->count('id')) {
            $time = Carbon::now();
            $data = [
                'code' => $time->year . $time->month . $time->day . random_int(1000, 9999),
                'title' => $validated['title'],
                'amount' => $validated['amount'],
                'min_total' => $validated['min_total'],
                'description' => $validated['description'] ?: null,
                'expiration_at' => isset($validated['expiration_at']) ? Carbon::createFromTimestamp(intval($validated['expiration_at'] / 1000), config('app.timezone')) : null,
                'related_action' => $validated['related_action'] ?: null,
                'updated_by' => $request->input('user')
            ];

            $coupon = Coupon::create($data);

            if (!$coupon) {
                throw new BusinessException(ResponseEnum::HTTP_ERROR);
            }

            return $this->success();
        }

        throw new BusinessException(ResponseEnum::HTTP_ERROR, '当前品种已存在，请重新建立');
    }

    public function issueCouponToUser(Request $request, string $id)
    {
        $validated = arrHumpToLine($request->post());

        $coupon = Coupon::findOrFail($id);

        $insertData = [];
        $time = Carbon::now();
        foreach ($validated['users'] as $user_id) {
            $insertData[] = [
                'coupon_code' => $coupon->code,
                'code' => $time->year . $time->month . $time->day . random_int(100000, 999999) . $user_id,
                'user_id' => $user_id,
                'title' => $coupon->title,
                'amount' => $coupon->amount,
                'min_total' => $coupon->min_total,
                'description' => $coupon->description,
                'expiration_at' => $coupon->expiration_at,
                'status' => false,
                'is_get' => false,
                'created_at' => $time->toDateTimeString(),
                'updated_at' => $time->toDateTimeString()
            ];
        }

        ClientUserCoupon::insert($insertData);

        return $this->success();
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
