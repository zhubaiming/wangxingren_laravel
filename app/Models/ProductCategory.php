<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class ProductCategory extends CommentsModel
{
    protected $table = 'sys_product_category';

    /**
     * 作用域一个查询以只包括热门用户。
     */
    public function scopeRoot(Builder $query): void
    {
        $query->where('parent_id', 0);
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

    public function trademark()
    {
        return $this->belongsToMany(ProductTrademark::class, 'sys_pivot_product_tardmark_category', 'category_id', 'trademark_id', 'id', 'id');
    }
}