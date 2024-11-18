<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SysGoodsBrand extends CommentsModel
{
    protected $table = 'sys_goods_brand';

    // ==============================  关联  ==============================
    // 多对多
    public function categorys(): BelongsToMany
    {
        return $this->belongsToMany(SysGoodsCategory::class, 'sys_pivot_goods_category_brand', 'category_id', 'brand_id');
    }
}
