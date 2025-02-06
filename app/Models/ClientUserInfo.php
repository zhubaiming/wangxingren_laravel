<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUserInfo extends CommentsModel
{
    protected $table = 'client_user_info';

    protected $attributes = [
        'is_register' => false
    ];

    protected function casts(): array
    {
        return [
            'is_register' => 'boolean',
            'device' => 'array',
            'system' => 'array'
        ];
    }

    // ==============================  关联  ==============================
    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }
}
