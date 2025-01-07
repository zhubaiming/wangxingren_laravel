<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ClientUserPoll extends Model
{
    use HasFactory;

    protected $table = 'client_user_poll';

    public $timestamps = false;

    protected $guarded = [];

    // ==============================  本地作用域  ==============================
    public function scopeOwner(Builder $query): void
    {
        $query->where(['user_id' => Auth::guard('wechat')->user()->id]);
    }
}
