<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payload = [
            ['id' => 1, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/2a8405c7-eef9-4c89-90c5-e8132b30e386.jpg', 'title' => '猫咪洗护' . $request->input('category_id'), 'price' => '100.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 2, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/55cd2327-c148-42ac-b315-ebf4cfa8c6b1.jpg', 'title' => '猫咪精致洗护' . $request->input('category_id'), 'price' => '180.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 3, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/728f223d-4603-49dc-8b0f-6674087280a0.jpg', 'title' => '小型犬洗护' . $request->input('category_id'), 'price' => '50.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 4, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/10f11160-f72b-4d4f-8a9a-4c66038fe7a4.jpg', 'title' => '小型犬精致洗护' . $request->input('category_id'), 'price' => '100.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 5, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/3a8d2155-27ca-4c0d-8272-f85234e60d40.jpg', 'title' => '大型犬洗护' . $request->input('category_id'), 'price' => '100.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 5, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/3a8d2155-27ca-4c0d-8272-f85234e60d40.jpg', 'title' => '大型犬精致洗护' . $request->input('category_id'), 'price' => '200.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 5, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/3a8d2155-27ca-4c0d-8272-f85234e60d40.jpg', 'title' => '大型犬精致洗护' . $request->input('category_id'), 'price' => '200.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 5, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/3a8d2155-27ca-4c0d-8272-f85234e60d40.jpg', 'title' => '大型犬精致洗护' . $request->input('category_id'), 'price' => '200.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 5, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/3a8d2155-27ca-4c0d-8272-f85234e60d40.jpg', 'title' => '大型犬精致洗护' . $request->input('category_id'), 'price' => '200.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
            ['id' => 5, 'cover' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/3a8d2155-27ca-4c0d-8272-f85234e60d40.jpg', 'title' => '大型犬精致洗护' . $request->input('category_id'), 'price' => '200.00', 'origin_price' => '150.00', 'sales_volume' => '9999+'],
        ];

        foreach ($payload as $key => $value) {
            $payload[$key] = arrLineToHump($value);
        }

        return $this->success(data: $payload);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $payload = [
            'id' => $id,
            'category_id' => $request->input('cateId'),
            'title' => 'test_猫咪洗护_' . $id,
            'sales_volume' => '9999+',
            'price' => '100.00',
            'origin_price' => '150.00',
            'score' => 4.1,
            'image_list' => [
                'https://crm.misswhite.com.cn/storage/topic/6459cc63daedf.jpg',
                'https://crm.misswhite.com.cn/storage/topic/6459cc6b1745a.jpg',
                'https://crm.misswhite.com.cn/storage/topic/6459cc78ddfb0.jpg',
                'https://crm.misswhite.com.cn/storage/topic/6459cc7dd5710.jpg'
            ],
            'detail' => '<h1 label="标题居中" style="font-size: 32px; font-weight: bold; border-bottom: 2px solid rgb(204, 204, 204); padding: 0px 4px 0px 0px; text-align: center; margin: 0px 0px 20px;">我是商品详情</h1><p><img src="https://crm.misswhite.com.cn/storage/topic/62eb864907e2a.png" width="100%"/></p>',
            'applicable' => [
                'category' => 1,
                'weight_type' => 2
            ]
        ];

        return $this->success(data: arrLineToHump($payload));
    }

    public function category()
    {
        $payload = [
            ['id' => 1, 'title' => '美白专区'],
            ['id' => 2, 'title' => '眼部护理'],
            ['id' => 3, 'title' => '清洁补水'],
            ['id' => 4, 'title' => '美甲美妆'],
            ['id' => 5, 'title' => '夏季脱毛'],
            ['id' => 6, 'title' => '祛痘专区'],
            ['id' => 7, 'title' => '修复专区'],
            ['id' => 8, 'title' => '头皮护理'],
            ['id' => 9, 'title' => '飞顿仪器专区'],
            ['id' => 10, 'title' => '医学护肤']
        ];

        return $this->success(data: $payload);
    }
}
