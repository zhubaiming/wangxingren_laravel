<?php

namespace App\Services\Order\Plugins;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

class GenerateTransactionsNoPlugin implements PluginInterface
{

    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();

        $out_trade_no = $parameters['time']->isoFormat('YYYYMMDD') . $parameters['time']->getPreciseTimestamp(3) . str_pad(1, 4, '0', STR_PAD_LEFT) . random_int(100000, 999999);
        $out_trade_no .= generateLuhnCheckDigit($out_trade_no);

        $patchwerk->mergeParameters([
            'trade_no' => $out_trade_no
        ]);

        return $next($patchwerk);
    }
}
