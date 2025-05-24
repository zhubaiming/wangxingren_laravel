<?php

namespace App\Services\Order\Plugins;

use App\Models\ClientUserOrder;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

class GenerateOrderNoPlugin implements PluginInterface
{

    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();

        // 随机码
        $randomCode = intval($parameters['time']->diffInSeconds($parameters['init_time'], true)) % 1000;

        $count = ClientUserOrder::whereBetween('created_at', [$parameters['time']->startOfDay(), $parameters['time']->endOfDay()])->count('id');
        $sequence = $count + 1;

        $orderSqe = null;
        $randomCodeArr = str_split(str_pad(strval($randomCode), 3, '0', STR_PAD_LEFT));
        foreach ([substr($sequence, 0, -2), substr($sequence, -2, -1), substr($sequence, -1)] as $idx => $val) {
            $orderSqe .= ($val === '' ? null : $val) . $randomCodeArr[$idx];
        }

        $reverseUserId = reverseNumberMath($parameters['user_id'] % 1000);

        $code = '1' . $orderSqe . ($parameters['type'] === 'backend' ? '0' : '1') . str_pad(strval($parameters['user_id'] % 100), 2, '0', STR_PAD_LEFT) . str_pad(strval($reverseUserId), 3, '0', STR_PAD_RIGHT);

        $code .= generateLuhnCheckDigit($code);

        if (strlen($code) < 10) {
            $pow = 10 - strlen($code);
            $code .= random_int((10 ** ($pow - 1)), (10 ** $pow - 1));
        }

        $patchwerk->mergeParameters([
            'order_no' => intval($code)
        ]);

        return $next($patchwerk);
    }
}
