<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SysGoodsCategory extends CommentsModel
{
    protected $table = 'sys_goods_category';

    // 属性类型转换
    protected function casts()
    {
        return [
            'parent_id' => 'integer',
            'is_parent' => 'boolean',
            'sort' => 'integer'
        ];
    }

    protected static function booted()
    {
        // ==============================  匿名全局作用域  ==============================
        static::addGlobalScope('sort', function (Builder $builder) {
            $builder->orderBy('sort', 'asc');
        });

        parent::booted();
    }

    // ==============================  关联  ==============================

    // **********  父子关联定义  ********
    public function parent()
    {
        return $this->belongsTo($this, 'parent_id', 'id');
    }

    // 定义与子分类的关系
    public function children()
    {
        return $this->hasMany($this, 'parent_id', 'id');
    }

    // 定义递归加载子分类
    public function childrenRecursive()
    {
        return $this->children()->with(['childrenRecursive']);
    }

    // 多对多(属于)
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(SysGoodsBrand::class, 'sys_pivot_goods_category_brand', 'category_id', 'brand_id');
    }

    // ==============================  其他  ==============================
}
