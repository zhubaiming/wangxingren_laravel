<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\PetTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Pet extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'user_pets';

    protected $attributes = [
        'is_default' => false,
        'remark' => null
    ];

    protected $guarded = [
        "deleted_at"
    ];

    // ==============================  属性类型转换  ==============================
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    // ==============================  本地作用域  ==============================
    public function scopeOwner(Builder $query): void
    {
        $query->where(['user_id' => Auth::guard('wechat')->user()->id]);
    }

    public function scopeIsDefault(Builder $query): void
    {
        $query->where(['is_default' => true]);
    }

    // ==============================  关联  ==============================
    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id', 'id');
    }

    // ==============================  访问器/修改器  ==============================
    protected function gender(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => GenderEnum::from($value)->name('animal')
        );
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => PetTypeEnum::from($value)->name()
        );
    }

    protected function weight(): Attribute
    {
        return Attribute::make(
            get: fn(int|null $value) => is_null($value) ? $value : floatval(bcdiv($value, '100', 2)),
            set: fn(float|null $value) => is_null($value) ? $value : intval(bcmul($value, '100', 0))
        );
    }
}
