<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSpecGroup extends Model
{
    use HasFactory;

    public function spu()
    {
        return $this->belongsTo(TestSpu::class, 'spu_id', 'id');
    }

    public function specValues()
    {
        return $this->hasMany(TestSkuSpecValue::class, 'spec_group_id', 'id');
    }
}
