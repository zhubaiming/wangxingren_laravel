<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SysPetBreed extends CommentsModel
{
    protected $table = 'sys_pet_breed';

    protected function casts()
    {
        return [
            'is_sync_attr' => 'boolean'
        ];
    }

    // ==============================  关联  ==============================
    public function spu(): BelongsToMany
    {
        return $this->belongsToMany(ProductSpu::class, 'pivot_product_spu_breed', 'breed_id', 'spu_id', 'id', 'id');
    }

    public function sku(): HasMany
    {
        return $this->hasMany(ProductSku::class, 'breed_id', 'id');
    }
}
