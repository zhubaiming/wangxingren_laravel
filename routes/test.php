<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/test_login', function (Request $request) {
    return \Illuminate\Support\Facades\Broadcast::auth($request);
});


Route::any('/goodSkuToProductSku', function () {
    $goodsSkus = \Illuminate\Support\Facades\DB::table('sys_goods_sku')->select('spu_id', 'spec_values')->get();

//    dd($goodsSkus->toArray());

    $i = 1;
    foreach ($goodsSkus as $goodsSku) {
        $spec_values = json_decode($goodsSku->spec_values, true);

        $data = [
            'spu_id' => $goodsSku->spu_id,
            'breed_id' => $spec_values['breed_id']
        ];

        if (isset($spec_values['weight_id'])) {
            $weight = \Illuminate\Support\Facades\DB::table('sys_pet_breed_weight')->select('min', 'max')->find($spec_values['weight_id']);

            $data['weight_min'] = $weight->min;
            $data['weight_max'] = $weight->max;
        }

        \Illuminate\Support\Facades\DB::table('sys_product_sku')->insert($data);

        $i++;
    }

    dd('完成，共 ' . $i . '条');
});

Route::any('/test_attr', function (Request $request) {
    $related = \App\Models\SysPetBreed::class;

    /**
     * 插入，已完成
     */
//    $validated = arrHumpToLine($request->input());
//
//    $attrs = \App\Models\ProductAttr::get();
//    $pets = \App\Models\SysPetBreed::select('id')->get()->pluck('id')->toArray();
//
//    foreach (\App\Models\ProductAttr::get() as $attr) {
//        $attr->pivotValues($related)->attach($pets);
//    }

//    dd($validated, $pets);

    /**
     * 输出
     */
//    $attr = \App\Models\ProductAttr::find(1);

    $attrs = \App\Models\ProductAttr::with('aaa')->get();


    dd($attrs->toArray());
});

Route::any('/getPetsFromMyfoodiepet', function () {
    list($s1, $s2) = explode(' ', microtime());
    $microtime = sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'tenantid' => '00ae459e842642f78b9ab0d8e7c027b4',
        'appid' => '6259662812989361028'
    ])
        ->replaceHeaders([
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36'
        ])
        ->withQueryParameters([
            '_' => $microtime,
            'page' => 1,
            'size' => 999,
            'type' => 1
        ])->get('https://cdp.myfoodiepet.com/deepexi-dm-admin/api/v1/petBreed/page');

    if ($response->ok()) {
        dd($response->json()['payload']['content']);
    } else {
        dd('完蛋');
    }

    dd('完成');

    /**
     * ?
     * _=1729741375303
     * &page=1
     * &size=999
     * &type=2
     */
});

Route::get('strHumpToLine', function (Request $request) {
    return mb_strtolower(preg_replace('/([A-Z])/', '_$1', $request->input('str')));
});

Route::get('getMonthDay', function () {
    dd(getDate(strtotime('2024-11-18 21:00')));

    /**
     * "seconds"  秒的数字表示                                                       0 到 59
     * "minutes"  分钟的数字表示                                                     0 到 59
     * "hours"    小时的数字表示                                                      0 到 23
     * "mday"     月份中第几天的数字表示                                               1 到 31
     * "wday"     星期几的数字表示                                                    0（周日）到 6（周六）
     * "mon"      月份的数字表示                                                      1 到 12
     * "year"     4 位数字表示的完整年份                                               比如： 1999 或 2003
     * "yday"     一年中第几天的数字表示                                               0 到 365
     * "weekday"  星期几的完整文本表示                                                 Sunday 到 Saturday
     * "month"    月份的完整文本表示，比如 January 或 March                             January 到 December
     * 0          自从 Unix 纪元开始至今的秒数，和 time() 的返回值以及用于 date() 的值类似  系统相关，通常从 -2147483648 到 2147483647。
     */
    list(
        '0' => $timestamp,
        'seconds' => $seconds,
        'minutes' => $minutes,
        'hours' => $hours,
        'mday' => $mday,
        'wday' => $wday,
        'mon' => $mon,
        'year' => $year,
        'yday' => $yday,
        'weekday' => $weekday,
        'month' => $month
        ) = getDate();

//    dd($timestamp, $seconds, $minutes, $hours, $mday, $wday, $mon, $year, $yday, $weekday, $month);

    $days = intval(bcadd(bcsub(date('t'), $mday, 0), '1', 0));
    $start = date('Y-m-' . $mday);

    for ($i = 0; $i < $days; $i++) {
        $strtotime = strtotime("{$start} +{$i} day");

        list(
            'seconds' => $new_seconds,
            'minutes' => $new_minutes,
            'hours' => $new_hours,
            'mday' => $new_mday,
            'wday' => $new_wday,
            'mon' => $new_mon,
            'year' => $new_year,
            'yday' => $new_yday,
            'weekday' => $new_weekday,
            'month' => $new_month
            ) = getDate($strtotime);

        $result[] = [
            'id' => $i + 1,
            'year' => $new_year,
            'month' => $new_mon,
            'day' => $new_mday,
//            'title' => \App\Enums\WeekEnum::from(date('w', $strtotime))->name() . "\n" . date('m-d', $strtotime),
            'title' => __('common.week.' . $new_weekday) . "\n" . $new_mon . '-' . $new_mday,
            'times' => []
        ];

        for ($j = 8; $j < 22; $j++) {
//            dd($new_year, $year, $new_yday, $yday);
            if ($new_year === $year && $new_yday === $yday) { // 如果是今天
//                dump($hours, $j);
                if ($hours < $j) { // 如果设置的时间比当前时间晚
                    if ($minutes < 30) { // 如果设置的分钟比当前时间晚
                        $result[$i]['times'][] = ['id' => $i * $j, 'hour' => $j, 'minute' => '00', 'title' => $j . ':00', 'can' => true, 'state' => random_int(0, 1) === 1];
                    } else {
                        $result[$i]['times'][] = ['id' => $i * $j, 'hour' => $j, 'minute' => '00', 'title' => $j . ':00', 'can' => false, 'state' => random_int(0, 1) === 1];
                    }
                    $result[$i]['times'][] = ['id' => ($i * $j + 1), 'hour' => $j, 'minute' => '30', 'title' => $j . ':30', 'can' => true, 'state' => random_int(0, 1) === 1];
                } else {
                    $result[$i]['times'][] = ['id' => $i * $j, 'hour' => $j, 'minute' => '00', 'title' => $j . ':00', 'can' => false, 'state' => random_int(0, 1) === 1];
                    $result[$i]['times'][] = ['id' => ($i * $j + 1), 'hour' => $j, 'minute' => '30', 'title' => $j . ':30', 'can' => false, 'state' => random_int(0, 1) === 1];
                }
            } else {
                $result[$i]['times'][] = ['id' => $i * $j, 'hour' => $j, 'minute' => '00', 'title' => $j . ':00', 'can' => true, 'state' => random_int(0, 1) === 1];
                $result[$i]['times'][] = ['id' => ($i * $j + 1), 'hour' => $j, 'minute' => '30', 'title' => $j . ':30', 'can' => true, 'state' => random_int(0, 1) === 1];
            }
        }


//        ['id' => 1, 'hour' => '08', 'minute' => '00', 'title' => '08:00', 'state' => true],
//                ['id' => 2, 'hour' => '08', 'minute' => '30', 'title' => '08:30', 'state' => true],
//                ['id' => 3, 'hour' => '09', 'minute' => '00', 'title' => '09:00', 'state' => true],
//                ['id' => 4, 'hour' => '09', 'minute' => '30', 'title' => '09:30', 'state' => true],
//                ['id' => 5, 'hour' => '10', 'minute' => '00', 'title' => '10:00', 'state' => false],
//                ['id' => 6, 'hour' => '10', 'minute' => '30', 'title' => '10:30', 'state' => false],
//                ['id' => 7, 'hour' => '11', 'minute' => '00', 'title' => '11:00', 'state' => true],
//                ['id' => 8, 'hour' => '11', 'minute' => '30', 'title' => '11:30', 'state' => true],
//                ['id' => 9, 'hour' => '12', 'minute' => '00', 'title' => '12:00', 'state' => true],
//                ['id' => 10, 'hour' => '12', 'minute' => '30', 'title' => '12:30', 'state' => true],
//                ['id' => 11, 'hour' => '13', 'minute' => '00', 'title' => '13:00', 'state' => true],
//                ['id' => 12, 'hour' => '13', 'minute' => '30', 'title' => '13:30', 'state' => false],
//                ['id' => 13, 'hour' => '14', 'minute' => '00', 'title' => '14:00', 'state' => true],
//                ['id' => 14, 'hour' => '14', 'minute' => '30', 'title' => '14:30', 'state' => false],
//                ['id' => 15, 'hour' => '15', 'minute' => '00', 'title' => '15:00', 'state' => false],
//                ['id' => 16, 'hour' => '15', 'minute' => '30', 'title' => '15:30', 'state' => false],
//                ['id' => 17, 'hour' => '16', 'minute' => '00', 'title' => '16:00', 'state' => true],
//                ['id' => 18, 'hour' => '16', 'minute' => '30', 'title' => '16:30', 'state' => true],
//                ['id' => 19, 'hour' => '17', 'minute' => '00', 'title' => '17:00', 'state' => true],
//                ['id' => 20, 'hour' => '17', 'minute' => '30', 'title' => '17:30', 'state' => false],
//                ['id' => 21, 'hour' => '18', 'minute' => '00', 'title' => '18:00', 'state' => true],
//                ['id' => 22, 'hour' => '18', 'minute' => '30', 'title' => '18:30', 'state' => true],
//                ['id' => 23, 'hour' => '19', 'minute' => '00', 'title' => '19:00', 'state' => true],
//                ['id' => 24, 'hour' => '19', 'minute' => '30', 'title' => '19:30', 'state' => false],
//                ['id' => 25, 'hour' => '20', 'minute' => '00', 'title' => '20:00', 'state' => true],
//                ['id' => 26, 'hour' => '20', 'minute' => '30', 'title' => '20:30', 'state' => true],
//                ['id' => 27, 'hour' => '21', 'minute' => '00', 'title' => '21:00', 'state' => false],
//                ['id' => 28, 'hour' => '21', 'minute' => '30', 'title' => '21:30', 'state' => true]
    }
    return response()->json($result);
});

Route::any('/test', function (Request $request) {
//    dd(config('filesystems.disks.public.root'));
//    return response()->json([
//        'router' => 'api',
//        'ip' => $request->ip(),
//        'client_ip' => $request->getClientIp()
//    ]);

    for ($i = 312; $i < 409; $i++) {
        $res[] = ['service_time_id' => $i, 'stock' => rand(0, 2)];
    }
    return response()->json($res);
});

Route::post('/shop_service', function (Request $request, \App\Services\ShopServiceService $service) {
    dd($service->createServiceInfo($request->post(), false));
});

Route::any('/request', function (Request $request) {
    return response()->json([
        'code' => 200,
        'message' => __('http_response.Ïsuccess'),
        'payload' => [
            'content' => $request->method()
        ]
    ]);
});

Route::get('/spu', function () {
    $spec_groups = \App\Models\SysGoodsSpecGroup::with(['slotSpecs' => function ($query) {
        $query->whereIn('sku_id', [27359021564])->with(['specValue']);
    }])->get();

//    $spec_groups = \App\Models\SysGoodsSpecGroup::with(['slotSpecs.specValue'])->get();

    return response()->json($spec_groups);
});
Route::get('/spu/{id}', function ($id) {
    $tag = \App\Models\Demo\Tag::with(['posts', 'videos'])->find($id);

    return response()->json($tag);
});
Route::get('/spu/{id}/sku', function ($id) {
    $skus = \App\Models\SysGoodsSku::where(['spu_id' => $id])->get('id')->toArray();
    $sku_ids = array_column($skus, 'id');


    return response()->json($sku_ids);
});

Route::any('/timeSub', function () {
//    dd(strtotime('2024-11-14 13:19:30'), strtotime(date('Y-m-d H:i:s')));
    dd(bcsub(strtotime(date('Y-m-d H:i:s')), strtotime('2024-11-14 13:19:30'), 0));
});

Route::post('/ssqjCode', function (Request $request) {
    list('code' => $code, 'postcode' => $postcode, 'value' => $value) = $request->post();

    /**
     * [ // routes/test.php:216
     * "code" => array:4 [
     * 0 => "110000"
     * 1 => "110100"
     * 2 => "110101"
     * 3 => "110101001"
     * ]
     * "postcode" => "100010"
     * "value" => array:4 [
     * 0 => "北京市"
     * 1 => "北京市"
     * 2 => "东城区"
     * 3 => "东华门街道"
     * ]
     * ]
     */

    $province_id = \Illuminate\Support\Facades\DB::connection('mysql_demo')->table('region')->where(['title' => $value[0], 'code' => $code[0], 'level' => 1, 'parent_id' => 0])->value('id');
    if (is_null($province_id)) {
        $province_id = \Illuminate\Support\Facades\DB::connection('mysql_demo')->table('region')->insertGetId(
            ['title' => $value[0], 'level' => 1, 'parent_id' => 0, 'code' => $code[0]]
        );
    }

    $city_id = \Illuminate\Support\Facades\DB::connection('mysql_demo')->table('region')->where(['title' => $value[1], 'code' => $code[1], 'level' => 2, 'parent_id' => $province_id])->value('id');
    if (is_null($city_id)) {
        $city_id = \Illuminate\Support\Facades\DB::connection('mysql_demo')->table('region')->insertGetId(
            ['title' => $value[1], 'level' => 2, 'parent_id' => $province_id, 'code' => $code[1]]
        );
    }

    $district_id = \Illuminate\Support\Facades\DB::connection('mysql_demo')->table('region')->where(['title' => $value[2], 'code' => $code[2], 'level' => 3, 'parent_id' => $city_id])->value('id');
    if (is_null($district_id)) {
        $district_id = \Illuminate\Support\Facades\DB::connection('mysql_demo')->table('region')->insertGetId(
            ['title' => $value[2], 'level' => 3, 'parent_id' => $city_id, 'code' => $code[2]]
        );
    }

    $street = \Illuminate\Support\Facades\DB::connection('mysql_demo')->table('region')->where(['title' => $value[3], 'code' => $code[3], 'level' => 4, 'parent_id' => $district_id])->value('id');
    if (is_null($street)) {
        \Illuminate\Support\Facades\DB::connection('mysql_demo')->table('region')->insert(
            ['title' => $value[3], 'level' => 4, 'parent_id' => $district_id, 'code' => $code[3], 'post_code' => $postcode]
        );
    }

    return response()->json([
        'code' => 200,
        'message' => 'success'
    ]);
});

Route::get('/getNetImage', function () {
    try {
//        dd(1);
        $results = generateAllCombinations();

        dd($results);
    } catch (Exception $e) {
        dd("Error: " . $e->getMessage());
    }
});

function generateAllCombinations()
{
    $alphabet = array_merge(range('a', 'z'), range('A', 'Z')); // 52 字母表
    $base = count($alphabet); // 进制为 52
    $length = 10; // 字符串长度
    $start = str_repeat($alphabet[0], $length); // 初始字符串：aaaaaaaaaa
    $end = str_repeat($alphabet[$base - 1], $length); // 结束字符串：ZZZZZZZZZZ

    $current = $start; // 当前字符串
    $results = [];

    do {
        dump($current);
//        $results[] = $current; // 将当前字符串存入结果集
        $current = incrementString($current, $alphabet, $base); // 递增字符串
    } while ($current !== $end);

//    $results[] = $end; // 加上最后一个字符串
    return $results;
}

function incrementString($string, $alphabet, $base)
{
    $chars = str_split($string); // 将字符串分解为数组
    $length = count($chars);
    $carry = true;

    for ($i = $length - 1; $i >= 0; $i--) {
        if ($carry) {
            $index = array_search($chars[$i], $alphabet);
            if ($index === $base - 1) { // 当前位为 Z，需进位
                $chars[$i] = $alphabet[0];
                $carry = true; // 继续向前进位
            } else { // 其他情况，直接递增
                $chars[$i] = $alphabet[$index + 1];
                $carry = false;
            }
        }
    }

    if ($carry) { // 如果最高位需要进位，超出长度限制
        throw new Exception("String exceeds maximum range!");
    }

    return implode('', $chars); // 拼接成新的字符串
}

Route::get('test_queue', function () {
    $num = rand(1, 9999999999);
    \App\Jobs\TestJob::dispatch($num);

    return response()->json('已添加队列');
});

Route::get('insertTradeDate', function () {
    $now = time();
    $start = mktime(0, 0, 0, 1, 1, 2024);

    $insert = [];
    for ($i = 0; $i < 4026; $i++) {
        $insert[] = [
            'date' => date('Y-m-d', strtotime('+' . $i . ' day', $start)),
            'order_count' => 0,
            'status' => true,
            'created_at' => date('Y-m-d H:i:s', $now)
        ];
    }

    \Illuminate\Support\Facades\DB::table('sys_trade_date')->insert($insert);
    dd($insert);
});

Route::get('/a1', function () {
    $model = new \App\Models\CompanyInfo();

    $model->trade_time_start = '09:00';
    $model->trade_time_end = '19:00';

    $model->images = [
        'https://27847557.s21i.faiusr.com/2/ABUIABACGAAg-v2ztAYorvep4gcw0A848AY!1000x1000.jpg',
        'https://27847557.s21i.faiusr.com/2/ABUIABACGAAg0IG0tAYosK3UygEw1g04uQY!1000x1000.jpg',
        'https://27847557.s21i.faiusr.com/2/ABUIABACGAAg0IG0tAYoveCZkwIwyAs42wc!1000x1000.jpg',
        'https://27847557.s21i.faiusr.com/2/ABUIABACGAAg0IG0tAYo-JfizgEwoAs4wAc!1000x1000.jpg',
        'https://27847557.s21i.faiusr.com/2/ABUIABACGAAg1YG0tAYo7IC81AYwpA046Ac!1000x1000.jpg'
    ];

    $model->description = '<h1 label="标题居中" style="font-size: 32px;font-weight: bold;border-bottom: 2px solid rgb(204, 204, 204);padding: 0px 4px 0px 0px;text-align: center;margin: 0px 0px 20px;">我是商品详情</h1><p><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://res.cngoldres.com/upload/2017/1011/30b836444f0411260a0afe76ea9576af.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://x0.ifengimg.com/ucms/2021_04/C63B2D16D69D691EDC7D8C31E299988487E2C3C5_size144_w1080_h1402.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://img2.baidu.com/it/u=2362882516,1616341177&fm=253&fmt=auto&app=138&f=JPEG?w=855&h=500" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://res.cngoldres.com/upload/2017/1011/30b836444f0411260a0afe76ea9576af.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://res.cngoldres.com/upload/2017/1011/30b836444f0411260a0afe76ea9576af.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://res.cngoldres.com/upload/2017/1011/30b836444f0411260a0afe76ea9576af.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://res.cngoldres.com/upload/2017/1011/30b836444f0411260a0afe76ea9576af.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://res.cngoldres.com/upload/2017/1011/30b836444f0411260a0afe76ea9576af.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://res.cngoldres.com/upload/2017/1011/30b836444f0411260a0afe76ea9576af.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div><div style="width: 100%;margin: 5px 0px 25px;overflow: hidden;border-radius: 10px;box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.12);"><img src="https://res.cngoldres.com/upload/2017/1011/30b836444f0411260a0afe76ea9576af.jpg" style="width: 100%;height: 100%;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;" /><div style="width: 100%;overflow: hidden;margin-top: 6px;display: flex;justify-content: space-between;align-items: center;"><div style="display:flex;flex-direction:column;"><span style="font-weight: 600;font-size: 18px;">White Smith Villa</span><span style="font-size: 14px">Meriose Venuse</span></div><div style="background-color: rgb(247, 208, 175);color: rgb(223, 112, 91);padding: 6px;border-radius: 4px;"><span>$ 250/mo</span></div></div></div></p>';

    $model->save();
});

Route::get('/a2', function () {
    $a = \App\Models\ClientUserLoginInfo::orderBy('id', 'asc')->get();

    foreach ($a as $aa) {
        \App\Models\ClientUserInfo::create([
            'user_id' => $aa->user_id,
            'app_type' => $aa->app_type,
            'appid' => $aa->appid,
            'openid' => $aa->openid,
            'unionid' => $aa->unionid,
            'is_register' => $aa->is_register,
            'device' => [],
            'system' => [],
            'created_at' => $aa->created_at,
            'updated_at' => $aa->updated_at,
        ]);
    }
});

Route::get('/a3', function () {
    $pets = [];
    $weightRange = [5, 8, 11, 16, 21, 26, 30, 40, 60, 80, 100];
    $now = \Carbon\Carbon::now();

    $cats = \App\Models\SysPetBreed::where('type', 1)->orderBy('letter', 'asc')->get();

    foreach ($cats as $cat) {
        $pets[] = [
            'user_id' => 3,
            'breed_id' => $cat->id,
            'breed_title' => $cat->title,
            'name' => '系统添加-' . $cat->title,
            'breed_type' => $cat->type,
            'gender' => 0,
            'weight' => 500,
            'color' => null,
            'avatar' => null,
            'remark' => null,
            'is_sterilization' => false,
            'is_default' => false,
            'birth' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    $dogs = $cats = \App\Models\SysPetBreed::where('type', 2)->orderBy('letter', 'asc')->get();

    foreach ($dogs as $dog) {
        foreach ($weightRange as $wight) {
            $pets[] = [
                'user_id' => 3,
                'breed_id' => $dog->id,
                'breed_title' => $dog->title,
                'name' => '系统添加-' . $dog->title . '-(' . \App\Enums\PetWeightRangeEnum::from($wight)->name() . ')',
                'breed_type' => $dog->type,
                'gender' => 0,
                'weight' => $wight * 100,
                'color' => null,
                'avatar' => null,
                'remark' => null,
                'is_sterilization' => false,
                'is_default' => false,
                'birth' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
    }

//    dd(\App\Models\ClientUserPet::where('user_id',3)->pluck('id')->toArray());
    \App\Models\ClientUserPet::destroy(\App\Models\ClientUserPet::where('user_id', 3)->pluck('id')->toArray());
    \App\Models\ClientUserPet::insert($pets);
    dd('批量添加成功');
});