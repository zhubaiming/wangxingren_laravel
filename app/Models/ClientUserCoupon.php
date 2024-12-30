<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUserCoupon extends Model
{
    use HasFactory;

    protected $table = 'client_user_coupon';

    protected function casts()
    {
        return [
            'status' => 'boolean'
        ];
    }
}
