<?php

namespace App\Models;

use App\Models\CommentsModel;

class ProductCategory extends CommentsModel
{
    protected $table = 'sys_product_category';

    /**
     * 作用域一个查询以只包括热门用户。
     */
    public function scopeRoot(Builder $query): void
    {
        $query->where('parent_id', '=', 0);
//        $query->where(['parent_id' => 0]);
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
}