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
        $validate = arrHumpToLine($request->input());

        ['banners' => $banners, 'index_reach' => $index_reach] = $validate;

        System::where('key', 'APP_BANNER')->update(['value' => json_encode($banners, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        System::where('key', 'APP_INDEX')->update(['value' => $index_reach]);

        return $this->success();
    }

}
