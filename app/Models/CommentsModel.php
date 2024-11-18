<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentsModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'deleted_at'
    ];

    /**
     * 准备一个日期进行数组 / JSON 序列化
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static function booted()
    {
        // ==============================  匿名全局作用域  ==============================
        static::addGlobalScope('defaultSort', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
}
