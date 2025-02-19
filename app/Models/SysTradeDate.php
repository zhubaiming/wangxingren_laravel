<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class SysTradeDate extends CommentsModel
{
    protected $table = 'sys_trade_date';

    protected function casts()
    {
        return [
//            'date' => 'date:Y-n-j',
            'date' => 'date:Y-m-d',
            'status' => 'boolean'
        ];
    }

    /**
     * 订单数量
     *
     * @return HasMany
     */
    public function userOrder(): HasMany
    {
        return $this->hasMany(ClientUserOrder::class, 'reservation_date', 'date');
    }
}
