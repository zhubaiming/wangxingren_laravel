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
        $payload = System::select('key', 'value')->where('key', 'APP_POLL')->first()->toArray();

        $payload['value'] = json_decode($payload['value'], true);

        return $this->success(arrLineToHump($payload));
    }

    public function appPollUpdate()
    {

    }

    public function companyIndex()
    {
        $payload = System::select('key', 'value')->where('key', 'COMPANY_BANNER')->orWhere('key', 'COMPANY_INDEX')->orWhere('key', 'COMPANY_TRADE_TIMES')->get();

        [$company_banner, $company_index, $company_trade_times] = $payload->toArray();

        $company_banner['value'] = json_decode($company_banner['value'], true);
        $company_trade_times['value'] = json_decode($company_trade_times['value'], true);

        return $this->success(arrLineToHump(compact('company_banner', 'company_index', 'company_trade_times')));
    }

    public function companyUpdate(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        ['banners' => $banners, 'index_reach' => $index_reach, 'time_start' => $time_start, 'time_end' => $time_end] = $validated;

        System::where('key', 'COMPANY_BANNER')->update(['value' => json_encode($banners, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        System::where('key', 'COMPANY_INDEX')->update(['value' => $index_reach]);
        System::where('key', 'COMPANY_TRADE_TIMES')->update(['value' => json_encode(['time_start' => $time_start, 'time_end' => $time_end], JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);

        return $this->success();
    }
}
