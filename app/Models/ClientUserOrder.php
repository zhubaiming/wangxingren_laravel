<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUserOrder extends CommentsModel
{
    protected $table = 'client_user_order';

    protected $guarded = [];

    // ==============================  关联  ==============================
    public function user()
    {
        return $this->belongsTo(ClientUser::class, 'id', 'user_id');
    }

    public function spu(): BelongsTo
    {
        return $this->belongsTo(ProductSpu::class, 'goods_id', 'id');
//        return $this->belongsTo(ProductSpu::class, 'spu_id', 'id');
    }

    public function trademark()
    {
        return $this->belongsTo(ProductTrademark::class, 'trademark_id', 'id');
    }
}
