<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientUserLoginInfo extends CommentsModel
{
    protected $table = 'client_user_login_infos';

    protected $attributes = [
        'is_register' => false
    ];

    protected $with = ['deviceInfo'];

    // ==============================  关联  ==============================
    public function deviceInfo(): HasMany
    {
        return $this->hasMany(ClientUserDeviceInfo::class, 'user_login_info_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }
}
