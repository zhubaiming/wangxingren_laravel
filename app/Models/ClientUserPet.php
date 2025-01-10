<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ClientUserPet extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'client_user_pets';

    protected $attributes = [
        'is_default' => false,
        'remark' => null
    ];

    protected $guarded = ['deleted_at'];

    // ==============================  属性类型转换  ==============================
    protected function casts(): array
    {
        return [
            'avatar' => 'array',
            'is_sterilization' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    // ==============================  本地作用域  ==============================
    public function scopeOwner(Builder $query): void
    {
        $query->where('user_id', Auth::guard('wechat')->user()->id);
    }

    // ==============================  关联  ==============================
    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }

    // ==============================  访问器/修改器  ==============================
    protected function weight(): Attribute
    {
        return Attribute::make(
            get: fn(int|null $value) => is_null($value) ? $value : applyIntegerToFloatModifier($value),
            set: fn(float|null $value) => is_null($value) ? $value : applyFloatToIntegerModifier($value),
        );
    }
}
