<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class ClientUserOrder extends Model
{
    protected $table = 'client_user_order';

    protected $guarded = [];

    // ==============================  属性类型转换  ==============================
    protected function casts(): array
    {
        return [
            'spu_json' => 'array',
            'sku_json' => 'array',
            'address_json' => 'array',
            'pet_json' => 'array',
            'coupon_json' => 'array',
        ];
    }

    // ==============================  本地作用域  ==============================
    public function scopeOwner(Builder $query): void
    {
        $query->where('user_id', Auth::guard('wechat')->user()->id);
    }

    // ==============================  关联  ==============================
    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'id', 'user_id');
    }

//    public function spu(): BelongsTo
//    {
//        return $this->belongsTo(ProductSpu::class, 'spu_id', 'id');
//    }

    public function trademark(): BelongsTo
    {
        return $this->belongsTo(ProductTrademark::class, 'trademark_id', 'id');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(ClientUserOrderRefund::class, 'order_id', 'id');
    }

    public function car()
    {
        return $this->belongsTo(ServiceCar::class, 'reservation_car', 'id');
    }
}
