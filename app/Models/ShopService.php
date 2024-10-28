<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopService extends Model
{
    use HasFactory;

    protected $table = 'shop_services';

    protected $attributes = [
        'is_saving' => true,
        'is_del' => false
    ];

    protected $guarded = [
        "is_del"
    ];
}
