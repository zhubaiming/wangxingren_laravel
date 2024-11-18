<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payload = [
            ['id' => 1, 'province' => '湖北省', 'city' => '武汉市', 'district' => '江夏区', 'street' => '', 'address' => '高新六路光谷一路交叉路口西南140米创美电玩', 'person_name' => '于生', 'person_phone_prefix' => '86', 'person_phone_number' => '13811111111', 'is_default' => true, 'tags' => []],
            ['id' => 2, 'province' => '湖南省', 'city' => '衡阳市', 'district' => '衡东县', 'street' => '', 'address' => '参合路568号', 'person_name' => '姜力群', 'person_phone_prefix' => '86', 'person_phone_number' => '17298060227', 'is_default' => false, 'tags' => []],
            ['id' => 3, 'province' => '北京市', 'city' => '直辖区', 'district' => '海淀区', 'street' => '青龙桥街道', 'address' => '水磨成府社区居委会 573', 'person_name' => '关晨', 'person_phone_prefix' => '86', 'person_phone_number' => '13452602601', 'is_default' => true, 'tags' => []],
            ['id' => 4, 'province' => '黑龙江省', 'city' => '哈尔滨市', 'district' => '尚志市', 'street' => '老街基乡', 'address' => '联丰村村委会 237', 'person_name' => '杜华', 'person_phone_prefix' => '86', 'person_phone_number' => '15576932879', 'is_default' => true, 'tags' => []],
            ['id' => 5, 'province' => '上海市', 'city' => '直辖区', 'district' => '松江区', 'street' => '广富林街道', 'address' => '悦都社区居委会 975', 'person_name' => '章云', 'person_phone_prefix' => '86', 'person_phone_number' => '13537035927', 'is_default' => true, 'tags' => []],
        ];

        return $this->success(arrLineToHump($payload));
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
