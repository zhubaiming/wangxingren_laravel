<?php

namespace App\Models;

use App\Models\Pivot\PivotProductAttrValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttr extends Model
{
    use HasFactory;

    protected $table = 'sys_product_attrs';

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function getPivotValues()
    {
        return $this->hasMany(PivotProductAttrValue::class, 'product_attr_id', 'id');
    }

    public function setPivotValues($related)
    {
        return $this->morphToMany($related, 'taggable', 'pivot_product_attr_value', 'product_attr_id', 'value_id', 'id', 'id', inverse: true);
    }
}
