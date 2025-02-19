<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysArea extends Model
{
    use HasFactory;

    protected $table = 'sys_area';

    protected function casts()
    {
        return [
            'status' => 'boolean',
            'deleted' => 'boolean',
        ];
    }
}
