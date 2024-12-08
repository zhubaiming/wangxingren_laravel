<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSku extends Model
{
    use HasFactory;

    public function spu()
    {
        return $this->belongsTo(TestSpu::class, 'spu_id', 'id');
    }

    public function specValues()
    {
//        return $this->belongsToMany(TestSpecValue::class, 'test_sku_spec_values', 'sku_id', 'spec_value_id');
        return $this->hasMany(TestSkuSpecValue::class, 'sku_id', 'id');
    }
}
