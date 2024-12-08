<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use App\Models\ClientUserOrder;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function info()
    {
        $register_user_total = ClientUser::count();

        $order_total = ClientUserOrder::whereBetween('created_at', [strtotime('today 00:00:00'), strtotime('today 23:59:59')])->where(['status' => 1])->count();

        $flow_total = 99999;


        return $this->success([
            'statistic' => [
                'register_user_total' => $register_user_total,
                'order_total' => $order_total,
                'flow_total' => $flow_total,
            ]
        ]);
    }
}
