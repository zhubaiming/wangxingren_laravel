<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ClientUserOrder extends Model
{
    protected $table = 'client_user_order';

    protected $guarded = [];

    // ==============================  本地作用域  ==============================
    public function scopeOwner(Builder $query): void
    {
        $query->where(['user_id' => Auth::guard('wechat')->user()->id]);
    }

    // ==============================  关联  ==============================
    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'id', 'user_id');
    }

    public function spu(): BelongsTo
    {
        return $this->belongsTo(ProductSpu::class, 'spu_id', 'id');
    }

    public function trademark(): BelongsTo
    {
        return $this->belongsTo(ProductTrademark::class, 'trademark_id', 'id');
    }
}
