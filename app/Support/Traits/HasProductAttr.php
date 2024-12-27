<?php

namespace App\Support\Traits;

use App\Models\ProductAttr;

trait HasProductAttr
{
    public function productAttr()
    {
        return $this->morphToMany(ProductAttr::class);
    }
}