<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSpecValue extends Model
{
    use HasFactory;

    public function specGroup()
    {
        return $this->belongsTo(TestSpecGroup::class, 'spec_group_id', 'id');
    }

    public function skus()
    {
        return $this->belongsToMany(TestSku::class, 'test_sku_spec_values', 'spec_value_id', 'sku_id');
    }
}
