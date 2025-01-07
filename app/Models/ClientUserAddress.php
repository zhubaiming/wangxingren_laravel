<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ClientUserAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'client_user_address';

    protected $guarded = [];

    protected function casts()
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    // ==============================  本地作用域  ==============================
    public function scopeOwner(Builder $query): void
    {
        $query->where('user_id', Auth::guard('wechat')->user()->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }
}
