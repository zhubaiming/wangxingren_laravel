<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sys_product_sku';

    protected $guarded = ['deleted_at'];

    // ==============================  属性类型转换  ==============================
    protected function casts(): array
    {
        return [
//            'weight' => 'integer',
//            'is_default' => 'boolean',
        ];
    }

    // ==============================  访问器/修改器  ==============================
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn(float $value) => floatval(applyIntegerToFloatModifier($value)),
            set: fn(float $value) => applyFloatToIntegerModifier($value)
        );
    }

    protected function priceConv(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => applyIntegerToFloatModifier($attributes['price'])
        );
    }

    protected function weightMin(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => applyIntegerToFloatModifier($value),
            set: fn(float $value) => applyFloatToIntegerModifier($value)
        );
    }

    protected function weightMax(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => applyIntegerToFloatModifier($value),
            set: fn(float $value) => applyFloatToIntegerModifier($value)
        );
    }
}
