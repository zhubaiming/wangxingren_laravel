<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientUserAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'client_user_address';

    protected $guarded = [];

    protected function casts()
    {
        return [
            'is_default' => 'boolean',
        ];
    }
}
