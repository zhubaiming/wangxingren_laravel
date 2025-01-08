<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ClientUserCoupon extends Model
{
    use HasFactory;

    protected $table = 'client_user_coupon';

    protected $guarded = ['deleted_at'];

    protected function casts()
    {
        return [
            'status' => 'boolean',
            'is_get' => 'boolean',
        ];
    }

    // ==============================  本地作用域  ==============================
    public function scopeOwner(Builder $query): void
    {
//        $query->where('user_id', Auth::guard('wechat')->user()->id);
        $query->where('user_id', 1);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }
}
