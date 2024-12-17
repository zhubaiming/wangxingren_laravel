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
}
