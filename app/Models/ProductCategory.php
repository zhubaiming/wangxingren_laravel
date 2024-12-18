<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class ProductCategory extends CommentsModel
{
    protected $table = 'sys_product_category';

    protected static function boot()
    {
        parent::boot();

        // 监听 deleting 事件
        static::deleting(function ($item) {
            $item->trademarks()->detach();
            // 递归删除所有子级
            foreach ($item->children as $child) {
                $child->delete();
            }
        });
    }

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

    public function trademarks()
    {
        return $this->belongsToMany(ProductTrademark::class, 'pivot_product_tardmark_category', 'category_id', 'trademark_id', 'id', 'id');
    }
}