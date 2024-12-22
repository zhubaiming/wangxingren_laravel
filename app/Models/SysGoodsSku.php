<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\MassPrunable;

class SysGoodsSku extends CommentsModel
{
    use MassPrunable;

    protected $table = 'sys_goods_sku';

    // 属性类型转换
    protected function casts()
    {
        return [
            'spu_id' => 'integer',
            'spec_group_id' => 'integer',
            'enable' => 'boolean',
            'price' => 'integer',
            'spec_values' => 'array'
        ];
    }

    protected $touches = ['spu'];

    protected $appends = ['price_conv'];

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
    protected function price(): Attribute
    {
        return Attribute::make(
            set: fn(float $value) => applyFloatToIntegerModifier($value)
        );
    }

    protected function priceConv(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => applyIntegerToFloatModifier($attributes['price'])
        );
    }

    // ==============================  关联  ==============================
//    // 一对多
//    public function slotSpecs(): HasMany // SKU与中间表一对多关系
//    {
//        return $this->hasMany(SysSlotGoodsSkuSpecGroup::class, 'sku_id', 'id');
//    }
//
//    // 反向
//    public function spu(): BelongsTo
//    {
//        return $this->BelongsTo(SysGoodsSpu::class, 'spu_id', 'id');
//    }
//
//    // 远程一对多
//    public function specGroups(): HasManyThrough // SKU通过中间表间接关联到规格组和规格值
//    {
//        return $this->hasManyThrough(SysGoodsSpecGroup::class, SysSlotGoodsSkuSpecGroup::class, 'sku_id', 'id', 'id', 'spec_group_id');
//    }

    // ==============================  关联(多对多)  ==============================
//    public function brands(): BelongsToMany
//    {
//        return $this->belongsToMany(SysGoodsServiceTime::class, 'sys_goods_sku_service_time_slots', 'sku_id', 'time_slot_id');
//    }


    // ==============================  关联(多态:多对多)  ==============================
//    public function specGroups()
//    {
//        return $this->belongsToMany(SysGoodsSpecGroup::class, 'sys_slot_goods_sku_spec_group', 'sku_id', 'spec_group_id')
//            ->withPivot('spec_value_id', 'taggable_type');
//    }
}
