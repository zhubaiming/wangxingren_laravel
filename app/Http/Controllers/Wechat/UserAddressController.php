<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\ClientUserAddress;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paginate = $request->has('paginate') ? isTrue($request->get('paginate')) : true; // 是否分页

        $query = ClientUserAddress::select('id', 'province', 'city', 'district', 'street', 'address', 'person_name', 'person_phone_prefix', 'person_phone_number', 'is_default')->where(['user_id' => Auth::guard('wechat')->user()->id])->orderBy('is_default', 'desc')->orderBy('created_at', 'asc');

        $payload = $paginate ? $query->simplePaginate($request->get('pageSize') ?? $this->pageSize, ['*'], 'page', $request->get('page') ?? $this->page) : $query->get();

        return $this->returnIndex($payload, 'Wechat\ClientUserAddressResource', __FUNCTION__, $paginate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = arrHumpToLine($request->post());

        $validate['user_id'] = Auth::guard('wechat')->user()->id;

        if ($validate['is_default']) {
            ClientUserAddress::where(['user_id' => Auth::guard('wechat')->user()->id, 'is_default' => true])->update(['is_default' => false]);
        }

        ClientUserAddress::create($validate);

        return $this->message('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /*
         * province 省
         * city     市
         * district 区
         * street   街道
         * address  详细地址与门牌号
         * person_name 联系人姓名
         * person_phone_prefix 联系电话前缀
         * person_phone_number 联系电话
         *
         * 湖北省武汉市江夏区高新六路光谷一路交叉路口西南140米创美电玩
         */

        $payload = ['id' => 1, 'province' => '湖北省', 'city' => '武汉市', 'district' => '江夏区', 'street' => '', 'address' => '高新六路光谷一路交叉路口西南140米创美电玩', 'person_name' => '于生', 'person_phone_prefix' => '86', 'person_phone_number' => '13811111111', 'is_default' => true, 'tags' => []];

        return $this->success(arrLineToHump($payload));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = arrHumpToLine($request->post());

        try {
            $address = ClientUserAddress::where(['id' => $id, 'user_id' => Auth::guard('wechat')->user()->id])->firstOrFail();

            if (isset($validate['is_default'])) {
                ClientUserAddress::where(['user_id' => Auth::guard('wechat')->user()->id, 'is_default' => true])->update(['is_default' => false]);
            }

            foreach ($validate as $field => $value) {
                $address->{$field} = $value;
            }

            $address->save();
        } catch (ModelNotFoundException) {
            return $this->message('地址不存在，请核实');
        }

        return $this->message('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $address = ClientUserAddress::where(['id' => $id, 'user_id' => Auth::guard('wechat')->user()->id])->firstOrFail();

            $address->delete();
        } catch (ModelNotFoundException) {
            return $this->message('地址不存在，请核实');
        }

        return $this->message('success');
    }
}
