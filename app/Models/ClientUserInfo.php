<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUserInfo extends Model
{
    use HasFactory;

    protected $table = 'wechat_user_infos';

    protected $attributes = [
        'is_del' => false
    ];

    protected $guarded = [
        "is_del"
    ];

    /**
     * 所有需要被触摸的关系名
     *
     * @var string[]
     */
    protected $touches = ['user'];

    // ==============================  关联  ==============================
    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }
}
