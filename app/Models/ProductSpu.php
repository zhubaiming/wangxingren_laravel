<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSpu extends CommentsModel
{
    protected $table = 'sys_product_spu';

    // 属性类型转换
    protected function casts()
    {
        return [
            'category_id' => 'integer',
            'brand_id' => 'integer',
            'saleable' => 'boolean',
            'sales_volume' => 'integer',
            'score' => 'integer',
            'images' => 'array',
        ];
    }

    // ==============================  关联  ==============================

    /**
     * 品牌 - 多对一
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(ProductTrademark::class, 'trademark_id', 'id');
    }

    /**
     * 分类 - 多对一
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    /**
     * 订单 - 一对多
     */
    public function order(): HasMany
    {
        return $this->hasMany(ClientUserOrder::class, 'spu_id', 'id');
    }

    /**
     * 品种 - 多对多
     */
    public function spu_breed(): BelongsToMany
    {
        return $this->belongsToMany(SysPetBreed::class, 'pivot_product_spu_breed', 'spu_id', 'breed_id', 'id', 'id');
    }

    /**
     * sku - 一对多
     */
    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class, 'spu_id', 'id');
    }
}
