<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysPivotGoodsSkuValue extends Model
{
    use HasFactory;

    protected $table = 'sys_pivot_goods_sku_value';

    public $timestamps = false;

    protected $guarded = [
        'deleted_at'
    ];

    // ==============================  关联  ==============================
//    public function specGroup() // 中间表与规格组的关系
//    {
//        return $this->belongsTo(SysGoodsSpecGroup::class, 'spec_group_id', 'id');
//    }

    // 多态
    public function specValue() // 根据 `taggable_type` 动态关联到宠物品种或体重
    {
        return $this->morphTo(null, 'taggable_type', 'spec_value_id');
    }
}
