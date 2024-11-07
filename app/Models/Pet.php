<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\PetCategory;
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

    protected $appends = ['gender_conv', 'category_conv', 'weight_type_conv'];

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
    protected function genderConv(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => GenderEnum::from($attributes['gender'])->name('animal')
        );
    }

    protected function categoryConv(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => PetCategory::from($attributes['category'])->name('animal')
        );
    }

    protected function weight(): Attribute
    {
        return Attribute::make(
            get: fn(int|null $value) => is_null($value) ? $value : floatval(bcdiv($value, '100', 2)),
            set: fn(float|null $value) => is_null($value) ? $value : intval(bcmul($value, '100', 0))
        );
    }

    protected function weightType(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value, array $attributes) {
                if ($attributes['category'] === 1) {
                    switch (true) {
                        case $attributes['weight'] > 1000: // big
                            return 3;
                        case $attributes['weight'] > 500: // middle
                            return 2;
                        case $attributes['weight'] > 0: // small
                        default:
                            return 1;
                    }
                } elseif ($attributes['category'] === 2) {
                    switch (true) {
                        case $attributes['weight'] > 1500:
                            return 3;
                        case $attributes['weight'] > 1000:
                            return 2;
                        case $attributes['weight'] > 0:
                        default:
                            return 1;
                    }
                }
            }
        );
    }

    protected function weightTypeConv(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return match ($attributes['weight_type']) {
                    1 => 'small',
                    2 => 'middle',
                    3 => 'big'
                };
            }
        );
    }
}
