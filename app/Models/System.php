<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory;

    protected $table = 'sys_system';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];
}
