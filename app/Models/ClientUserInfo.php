<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUserInfo extends CommentsModel
{
    protected $table = 'client_user_info';

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
