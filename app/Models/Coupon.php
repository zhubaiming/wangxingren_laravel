<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'sys_coupon';

    protected $guarded = ['deleted_at'];

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => applyIntegerToFloatModifier($value),
            set: fn(string $value) => applyFloatToIntegerModifier($value),
        );
    }

    protected function minTotal(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => applyIntegerToFloatModifier($value),
            set: fn(string $value) => applyFloatToIntegerModifier($value),
        );
    }
}
