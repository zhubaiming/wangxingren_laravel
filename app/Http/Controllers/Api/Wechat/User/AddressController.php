<?php

namespace App\Http\Controllers\Api\Wechat\User;

use App\Http\Controllers\Controller;
use App\Models\ClientUserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validate = arrHumpToLine($request->input());
        $paginate = isset($validate['paginate']) ? isTrue($validate['paginate']) : true; // 是否分页

        $query = ClientUserAddress::select('id', 'province', 'city', 'district', 'street', 'address', 'person_name', 'person_phone_prefix', 'person_phone_number', 'is_default')->where(['user_id' => Auth::guard('wechat')->user()->id])->orderBy('is_default', 'desc')->orderBy('created_at', 'asc');

        $payload = $paginate ? $query->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'Wechat\ClientUserAddressResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = arrHumpToLine($request->input());

        $data = [
//            'user_id' => Auth::guard('wechat')->user()->id,
            'country' => '中国',
            'province' => $validate['province'],
            'city' => $validate['city'],
            'district' => $validate['district'],
            'street' => $validate['street'] ?? null,
            'address' => $validate['address'],
            'is_default' => $validate['is_default'],
            'person_name' => $validate['person_name'],
            'person_phone_prefix' => $validate['person_phone_prefix'],
            'person_phone_number' => $validate['person_phone_number']
        ];

        Auth::guard('wechat')->user()->addresses()->createMany([$data]);

        $this->success();
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
