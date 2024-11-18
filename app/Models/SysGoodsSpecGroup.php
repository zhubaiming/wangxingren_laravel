<?php

namespace App\Models;

use App\Models\Pivot\SysPivotGoodsSpecGroupValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SysGoodsSpecGroup extends CommentsModel
{
    protected $table = 'sys_goods_spec_group';

    // ==============================  本地作用域  ==============================
    public function scopeIsParent(Builder $query)
    {
        $query->where('is_parent', true);
    }

    // ==============================  关联  ==============================
    // 多对多
    public function spus(): BelongsToMany
    {
        return $this->belongsToMany(SysGoodsSpu::class, 'sys_pivot_goods_spec_group_spu', 'spec_group_id', 'spu_id');
    }

    // 多态多对多
    public function slotSpecs(): HasMany // 规格组与中间表一对多关系
    {
        return $this->hasMany(SysPivotGoodsSpecGroupValue::class, 'spec_group_id', 'id');
    }
}
