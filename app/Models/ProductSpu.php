<?php

namespace App\Models;

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
    /*
     * 品牌 - 一对一
     */
    public function detail()
    {
        return $this->hasOne(ProductSpuDetail::class, 'spu_id', 'id');
    }

    public function trademark()
    {
        return $this->belongsTo(ProductTrademark::class, 'trademark_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function attr()
    {
        return $this->belongsToMany(ProductAttr::class, 'pivot_product_spu_attr', 'spu_id', 'attr_id', 'id', 'id');
    }

    public function order()
    {
        return $this->hasMany(ClientUserOrder::class, 'spu_id', 'id');
    }

    public function spu_breed()
    {
        return $this->belongsToMany(SysPetBreed::class, 'pivot_product_spu_breed', 'spu_id', 'breed_id', 'id', 'id');
    }

    public function skus()
    {
        return $this->hasMany(ProductSku::class, 'spu_id', 'id');
    }
}
