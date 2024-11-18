<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class SysPetBreed extends CommentsModel
{
    protected $table = 'sys_pet_breed';

    // ==============================  关联  ==============================
    // 一对多
    public function weights(): HasMany // 宠物品种与宠物重量的关系(一个品种有多个重量)
    {
        return $this->hasMany(SysPetBreedWeight::class, 'breed_id', 'id');
    }
}
