<?php

namespace App\Models\Slot;

use App\Models\SysGoodsSku;
use App\Models\SysGoodsSpecGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysSlotGoodsSkuSpecGroup extends Model
{
    use HasFactory;

    protected $table = 'sys_slot_goods_sku_spec_group';

    public $timestamps = false;

    protected $guarded = [
        'deleted_at'
    ];

    // ==============================  关联  ==============================
    // 反向
    public function sku() // 中间表与SKU的关系
    {
        return $this->belongsTo(SysGoodsSku::class, 'sku_id', 'id');
    }

    public function specGroup() // 中间表与规格组的关系
    {
        return $this->belongsTo(SysGoodsSpecGroup::class, 'spec_group_id', 'id');
    }

    // 多态
    public function specValue() // 根据 `taggable_type` 动态关联到宠物品种或体重
    {
        return $this->morphTo(null, 'taggable_type', 'spec_value_id');
    }
}
