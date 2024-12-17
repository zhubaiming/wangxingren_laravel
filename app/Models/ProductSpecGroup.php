<?php

namespace App\Models;

class ProductSpecGroup extends CommentsModel
{
    protected $table = 'sys_product_spec_group';

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
}