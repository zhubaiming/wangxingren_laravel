<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUserOrderRefund extends Model
{
    use HasFactory;

    protected $table = 'client_user_order_refund';

    protected $guarded = [];

    public function trademark(): BelongsTo
    {
        return $this->belongsTo(ClientUserOrder::class, 'order_id', 'id');
    }
}
