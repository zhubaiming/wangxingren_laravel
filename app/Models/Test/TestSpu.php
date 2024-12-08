<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSpu extends Model
{
    use HasFactory;

    public function skus()
    {
        return $this->hasMany(TestSku::class, 'spu_id', 'id');
    }

    public function specGroups()
    {
//        return $this->belongsToMany(TestSpecGroup::class, 'test_sku_spec_values', 'spu_id', 'spec_group_id')
//            ->withPivot('sku_id', 'spec_value_id');
        return $this->hasMany(TestSpecGroup::class, 'spu_id', 'id');
    }
}
