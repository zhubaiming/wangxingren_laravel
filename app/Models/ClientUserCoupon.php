<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }
}
