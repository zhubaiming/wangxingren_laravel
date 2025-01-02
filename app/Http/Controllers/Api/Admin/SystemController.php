<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\System;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function appIndexShow(Request $request)
    {
//        System::create([
//            'key' => 'APP_BANNER',
//            'value' => json_encode([
//                ['id' => '1', 'name' => 'a1', 'status' => 'finished', 'url' => 'https://dp-live.oss-cn-beijing.aliyuncs.com/marketing/image/hlDebwlcgq.jpg', 'has_video' => true, 'video_src' => 'http://wxsnsdy.tc.qq.com/105/20210/snsdyvideodownload?filekey=30280201010421301f0201690402534804102ca905ce620b1241b726bc41dcff44e00204012882540400&bizid=1023&hy=SH&fileparam=302c020101042530230204136ffd93020457e3c4ff02024ef202031e8d7f02030f42400204045a320a0201000400̰'],
//                ['id' => '2', 'name' => 'a2', 'status' => 'finished', 'url' => 'https://dp-live.oss-cn-beijing.aliyuncs.com/marketing/image/iDjKUTByaW.png', 'has_video' => false, 'video_src' => null],
//                ['id' => '3', 'name' => 'a3', 'status' => 'finished', 'url' => 'https://dp-live.oss-cn-beijing.aliyuncs.com/marketing/image/iKQig9Zfk0.jpg', 'has_video' => false, 'video_src' => null],
//            ], JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
//        ]);
//
//        System::create([
//            'key' => 'APP_INDEX',
//            'value' => '<p><img src="https://develop.wangxingren.fun/storage/index.jpg" width="100%"/></p>'
//        ]);


        $payload = System::select('key', 'value')->where('key', 'APP_BANNER')->orWhere('key', 'APP_INDEX')->get();

//        dd($payload->toArray());

        [$app_banner, $app_index] = $payload->toArray();

        $app_banner['value'] = json_decode($app_banner['value'], true);

        $banner_images = $banner_videos = [];
        foreach ($app_banner['value'] as $key => $value) {
            $banner_images[$key] = ['id' => $value['id'], 'name' => $value['name'], 'status' => $value['status'], 'url' => $value['url']];
            if (isset($value['video_src'])) {
                $banner_videos[] = [
                    'id' => $value['id'],
                    'index' => $key + 1,
                    'list' => [['id' => $value['id'], 'name' => $value['name'], 'status' => $value['status'], 'url' => $value['video_src']]]
                ];
            }
        }

//        dd($app_banner, $app_index);

        return $this->success(arrLineToHump(compact('app_banner', 'banner_images', 'banner_videos', 'app_index')));
    }
}
