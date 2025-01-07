<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\System;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function appIndexShow()
    {
        $payload = System::select('key', 'value')->where('key', 'APP_BANNER')->orWhere('key', 'APP_INDEX')->get();

        [$app_banner, $app_index] = $payload->toArray();

        $app_banner['value'] = json_decode($app_banner['value'], true);

        return $this->success(arrLineToHump(compact('app_banner', 'app_index')));
    }

    public function appIndexUpdate(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        ['banners' => $banners, 'index_reach' => $index_reach] = $validated;

        System::where('key', 'APP_BANNER')->update(['value' => json_encode($banners, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        System::where('key', 'APP_INDEX')->update(['value' => $index_reach]);

        return $this->success();
    }

    public function appPollIndex()
    {
//        $value = [
//            ['subject' => '您的宠物是猫猫，还是狗狗', 'type' => 'radio', 'sort' => 1, 'required' => true, 'options' => [['label' => '猫', 'value' => 1], ['label' => '狗', 'value' => 2], ['label' => '小型犬', 'value' => 3], ['label' => '大型犬', 'value' => 4], ['label' => '都有', 'value' => 5]]],
//            ['subject' => '您平时在哪里为您的宠物洗澡美容', 'type' => 'radio', 'sort' => 2, 'required' => true, 'options' => [['label' => '家里', 'value' => 1], ['label' => '宠物店', 'value' => 2], ['label' => '不一定', 'value' => 3]]],
//            ['subject' => '您宠物的洗澡美容间隔大概多久', 'type' => 'radio', 'sort' => 3, 'required' => true, 'options' => [['label' => '每周一次', 'value' => 1], ['label' => '每月一次', 'value' => 2], ['label' => '不固定看需求', 'value' => 3]]],
//            ['subject' => '您是如何知道移动宠物美容车的', 'type' => 'radio', 'sort' => 4, 'required' => true, 'options' => [['label' => '短视频媒体', 'value' => 1], ['label' => '广告', 'value' => 2], ['label' => '朋友介绍', 'value' => 3], ['label' => '路上偶遇', 'value' => 4]]],
//            ['subject' => '您觉得移动宠物美容车的价格合理吗', 'type' => 'radio', 'sort' => 5, 'required' => true, 'options' => [['label' => '合理', 'value' => 1], ['label' => '不合理', 'value' => 2], ['label' => '可以接受', 'value' => 3]]],
//            ['subject' => '如果移动宠物美容车推出充值活动，您愿意参加吗', 'type' => 'radio', 'sort' => 6, 'required' => true, 'options' => [['label' => '愿意', 'value' => 1], ['label' => '不愿意', 'value' => 2]]],
//            ['subject' => '如果我们把您的爱宠洗澡美容过程拍成端视频，您愿意发给朋友或短视频平台吗', 'type' => 'radio', 'sort' => 7, 'required' => true, 'options' => [['label' => '愿意', 'value' => 1], ['label' => '不愿意', 'value' => 2]]],
//            ['subject' => '您更看重移动宠物美容车服务的哪些方面', 'type' => 'checkbox', 'sort' => 8, 'required' => true, 'options' => [['label' => '专业的美容团队', 'value' => 1], ['label' => '上门服务的便利性', 'value' => 2], ['label' => '时间的灵活性', 'value' => 3], ['label' => '工具和用品的卫生情况', 'value' => 4]]],
//            ['subject' => '您愿意把移动宠物美容车推荐给身边的朋友吗', 'type' => 'radio', 'sort' => 9, 'required' => true, 'options' => [['label' => '愿意', 'value' => 1], ['label' => '不愿意', 'value' => 2]]],
//            ['subject' => '您觉得用小程序预约洗澡美容方便，还是电话微信预约方便', 'type' => 'radio', 'sort' => 10, 'required' => true, 'options' => [['label' => '小程序', 'value' => 1], ['label' => '微信电话', 'value' => 2]]],
//            ['subject' => '如果有好的投资方案，您愿意成为移动宠物美容车的合伙人吗', 'type' => 'radio', 'sort' => 11, 'required' => true, 'options' => [['label' => '愿意', 'value' => 1], ['label' => '不愿意', 'value' => 2]]],
//            ['subject' => '您是否有过因为宠物美容服务不满的经历？如果有，请您简单描述', 'type' => 'textarea', 'sort' => 12, 'required' => false],
//            ['subject' => '您对移动宠物美容车还有其他期待或建议吗', 'type' => 'textarea', 'sort' => 13, 'required' => false]
//        ];
//        
//        System::create([
//            'key' => 'APP_POLL',
//            'value' => json_encode($value, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
//        ]);

        $payload = System::select('key', 'value')->where('key', 'APP_POLL')->first()->toArray();

        $payload['value'] = json_decode($payload['value'], true);

        return $this->success(arrLineToHump($payload));
    }

    public function appPollUpdate()
    {

    }

    public function companyIndex()
    {
        $payload = System::select('key', 'value')->where('key', 'COMPANY_BANNER')->orWhere('key', 'COMPANY_INDEX')->get();

        [$company_banner, $company_index] = $payload->toArray();

        $company_banner['value'] = json_decode($company_banner['value'], true);

        return $this->success(arrLineToHump(compact('company_banner', 'company_index')));
    }

    public function companyUpdate(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        ['banners' => $banners, 'index_reach' => $index_reach] = $validated;

        System::where('key', 'COMPANY_BANNER')->update(['value' => json_encode($banners, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        System::where('key', 'COMPANY_INDEX')->update(['value' => $index_reach]);

        return $this->success();
    }
}
