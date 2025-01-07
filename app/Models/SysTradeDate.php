<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysTradeDate extends CommentsModel
{
    protected $table = 'sys_trade_date';

    protected function casts()
    {
        return [
            'date' => 'date:Y-n-j',
            'status' => 'boolean'
        ];
    }
}
