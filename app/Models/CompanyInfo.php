<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $table = 'sys_company_info';

    public $timestamps = false;

    protected $fillable = ['id', 'trade_time_start', 'trade_time_end', 'images', 'description'];

    protected function casts()
    {
        return [
            'images' => 'array',
        ];
    }
}
