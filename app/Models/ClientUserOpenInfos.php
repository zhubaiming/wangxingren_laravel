<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUserOpenInfos extends Model
{
    use HasFactory;

    protected $table = 'wechat_users_ids';

    protected $attributes = [
        'is_del' => false
    ];

    protected $guarded = [
        "is_del"
    ];

    protected $touches = ['user'];

    // ==============================  关联  ==============================
    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }
}
