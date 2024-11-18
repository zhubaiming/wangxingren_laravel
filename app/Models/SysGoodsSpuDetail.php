<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SysGoodsSpuDetail extends CommentsModel
{
    protected $table = 'sys_goods_spu_detail';

    protected $primaryKey = 'spu_id';

    // 属性类型转换
    protected function casts()
    {
        return [
            'images' => 'array',
        ];
    }

    protected $touches = ['spu'];

    // ==============================  关联(反向)  ==============================
    public function spu(): BelongsTo
    {
        return $this->BelongsTo(SysGoodsSpu::class, 'spu_id', 'id');
    }
}
