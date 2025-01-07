<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCategory extends Model
{
    use HasFactory;

    protected $table = 'sys_coupon_category';

    protected $guarded = ['deleted_at'];
}
