<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SysPetBreedWeight extends CommentsModel
{
    protected $table = 'sys_pet_breed_weight';

    // 属性类型转换
    protected function casts()
    {
        return [
            'breed_id' => 'integer',
            'min' => 'integer',
            'max' => 'integer',
            'weight_type' => 'integer'
        ];
    }

    // ==============================  访问器/修改器  ==============================
    protected function min(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => applyIntegerToFloatModifier($value),
            set: fn(float $value) => applyFloatToIntegerModifier($value)
        );
    }

    protected function max(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => applyIntegerToFloatModifier($value),
            set: fn(float $value) => applyFloatToIntegerModifier($value)
        );
    }

    // ==============================  关联  ==============================
    // 反向
    public function breed(): BelongsTo
    {
        return $this->belongsTo(SysPetBreed::class, 'breed_id', 'id');
    }
}
