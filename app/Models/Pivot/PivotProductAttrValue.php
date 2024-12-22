<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Model;

class PivotProductAttrValue extends Model
{
    protected $table = 'pivot_product_attr_value';

    public function bbb()
    {
//        return $this->hasOne()
        dd($this->getAttribute('taggable_type'));
    }
}