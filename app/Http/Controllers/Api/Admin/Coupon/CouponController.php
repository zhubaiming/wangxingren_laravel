<?php

namespace App\Http\Controllers\Api\Admin\Coupon;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
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
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = Coupon::orderBy('id', 'asc');

        $payload = $paginate ? $query->paginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'CouponResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = arrHumpToLine($request->post());

        if (0 === Coupon::where('title', $validate['title'])->count('id')) {

            $data = [
                'title' => $validate['title'],
                'amount' => $validate['amount'],
                'min_total' => $validate['min_total'],
                'expiration_at' => isset($validate['expiration_at']) ? Carbon::createFromTimestamp(intval($validate['expiration_at'] / 1000), config('app.timezone')) : null,
                'description' => $validate['description'] ?: null,
                'related_action' => $validate['related_action'] ?: null,
                'updated_by' => $request->input('user'),
            ];

            $coupon = Coupon::create($data);

            if (!$coupon) {
                throw new BusinessException(ResponseEnum::HTTP_ERROR);
            }

            return $this->success();
        }

        throw new BusinessException(ResponseEnum::HTTP_ERROR, '当前品种已存在，请重新建立');
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
