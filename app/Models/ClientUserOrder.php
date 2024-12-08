<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientUserOrder extends CommentsModel
{
    protected $table = 'client_user_order';

    // ==============================  关联  ==============================
    public function user()
    {
        return $this->belongsTo();
    }

    public function spu(): BelongsTo
    {
        return $this->belongsTo(SysGoodsSpu::class, 'goods_id', 'id');
    }
}
