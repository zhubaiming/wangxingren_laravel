<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SysGoodsSpu extends CommentsModel
{

    protected $table = 'sys_goods_spu';

    // 属性类型转换
    protected function casts()
    {
        return [
            'category_id' => 'integer',
            'brand_id' => 'integer',
            'saleable' => 'boolean',
            'sales_volume' => 'integer',
            'score' => 'integer',
        ];
    }

    protected static function booted()
    {
        // ==============================  匿名全局作用域  ==============================
        static::addGlobalScope('sort', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });

        parent::booted();
    }

    // ==============================  本地作用域  ==============================
    public function scopeSaleable(Builder $query)
    {
        $query->where('saleable', true);
    }

    // ==============================  关联  ==============================
    // 一对一
    public function detail(): HasOne // spu详情
    {
        return $this->hasOne(SysGoodsSpuDetail::class, 'spu_id', 'id');
    }

    // 一对多
    public function skus(): HasMany // spu下的sku
    {
        return $this->hasMany(SysGoodsSku::class, 'spu_id', 'id');
    }

    // 多对多
    public function specGroups(): BelongsToMany // spu包含规格
    {
        return $this->belongsToMany(SysGoodsSpecGroup::class, 'sys_pivot_goods_spec_group_value', 'spu_id', 'spec_group_id');
    }

    public function serviceTimes(): BelongsToMany // spu所包含服务时间
    {
        return $this->belongsToMany(SysGoodsServiceTime::class, 'sys_pivot_goods_service_time_spu', 'spu_id', 'service_time_id')->withPivot('stock');
    }

    /**
     * 订单
     */
    public function orders(): HasMany
    {
        return $this->hasMany(ClientUserOrder::class, 'goods_id', 'id');
    }
}
