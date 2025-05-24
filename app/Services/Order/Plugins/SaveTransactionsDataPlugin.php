<?php

namespace App\Services\Order\Plugins;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

class SaveTransactionsDataPlugin implements PluginInterface
{

    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        return $next($patchwerk);
    }
}
