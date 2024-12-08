<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSkuSpecValue extends Model
{
    use HasFactory;

    public function sku()
    {
        return $this->belongsTo(TestSku::class, 'sku_id', 'id');
    }

    public function specGroup()
    {
        return $this->belongsTo(TestSpecGroup::class, 'spec_group_id', 'id');
    }

    public function specValue()
    {
        return $this->morphTo();
    }
}
