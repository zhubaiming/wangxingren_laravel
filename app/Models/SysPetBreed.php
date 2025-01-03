<?php

namespace App\Models;

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
    // 一对多
    public function weights(): HasMany // 宠物品种与宠物重量的关系(一个品种有多个重量)
    {
        return $this->hasMany(SysPetBreedWeight::class, 'breed_id', 'id');
    }

    public function specGroup()
    {
        return $this->belongsToMany(ProductSpecGroup::class, 'sys_pivot_product_spec_group_value', 'spec_value_id', 'spec_group_id', 'id', 'id');
    }


    public function attrs()
    {
        return $this->morphToMany(ProductAttr::class, 'taggable', 'pivot_product_attr_value', 'value_id', 'product_attr_id', 'id', 'id');
    }

    public function spu()
    {
        return $this->belongsToMany(ProductSpu::class, 'pivot_product_spu_breed', 'breed_id', 'spu_id', 'id', 'id');
    }

    public function sku()
    {
        return $this->hasMany(ProductSku::class,'breed_id','id');
    }
}
