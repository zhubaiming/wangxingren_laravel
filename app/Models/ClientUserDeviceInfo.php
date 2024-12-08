<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUserDeviceInfo extends Model
{
    use HasFactory;

    protected $table = 'client_users_device_infos';

    protected $guarded = [];

    // ==============================  闭包事件  ==============================
    protected static function booted(): void
    {
        static::created(function (ClientUserDeviceInfo $device_info) {
            $device_info->loginInfo()->update(['user_device_info_id' => $device_info->id]);
        });
    }

    // ==============================  关联  ==============================
    public function loginInfo(): BelongsTo
    {
        return $this->belongsTo(ClientUserLoginInfo::class, 'user_login_info_id', 'id');
    }
}
