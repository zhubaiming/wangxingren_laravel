<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SysGoodsServiceTime extends CommentsModel
{
    protected $table = 'sys_goods_service_time';

    // 属性类型转换
    protected function casts()
    {
        return [
            'date' => 'datetime:Y-m-d',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'enable' => 'boolean',
        ];
    }

//    protected $appends = ['year', 'month', 'day'];

    protected static function booted()
    {
        // ==============================  匿名全局作用域  ==============================
        static::addGlobalScope('sort', function (Builder $builder) {
            $builder->orderBy('id', 'asc');
        });

        parent::booted();
    }

    // ==============================  本地作用域  ==============================
    public function scopeEnable(Builder $query)
    {
        $query->where('enable', true);
    }

    // ==============================  访问器/修改器  ==============================
    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('Y-m-d')
        );
    }

    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('H:i')
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('H:i')
        );
    }

    //  dd(getDate(strtotime('2024-11-18')));

//    protected function price(): Attribute
//    {
//        return Attribute::make(
//            set: fn(float $value) => applyFloatToIntegerModifier($value)
//        );
//    }
//
//    protected function year(): Attribute
//    {
//        return Attribute::make(
//            get: fn(mixed $value, array $attributes) => applyIntegerToFloatModifier($attributes['price'])
//        );
//    }

    // ==============================  关联  ==============================

    // ==============================  关联(多对多)  ==============================
//    public function brands(): BelongsToMany
//    {
//        return $this->belongsToMany(SysGoodsSku::class, 'sys_goods_sku_service_time_slots', 'sku_id', 'time_slot_id');
//    }
}
