<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestMemory extends Model
{
    use HasFactory;

    public function skus()
    {
        return $this->morphedByMany(TestSku::class, 'spec_value', 'test_sku_spec_values');
    }
}
