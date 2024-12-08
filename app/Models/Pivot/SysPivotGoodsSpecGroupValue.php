<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysPivotGoodsSpecGroupValue extends Model
{
    use HasFactory;

    protected $table = 'sys_pivot_goods_spec_group_value';

    public $timestamps = false;

    protected $guarded = [
        'deleted_at'
    ];

    // ==============================  关联  ==============================
    // 多态
    public function specValue() // 根据 `taggable_type` 动态关联到宠物品种或体重
    {
        return $this->morphTo(null, 'taggable_type', 'spec_value_id');
    }
}
