<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dict extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dicts';

    protected $guarded = ['deleted_at'];

    protected function casts()
    {
        return [
            'is_lock' => 'boolean'
        ];
    }
}
