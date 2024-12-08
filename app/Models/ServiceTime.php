<?php

namespace App\Models;

use App\Models\CommentsModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ServiceTime extends CommentsModel
{
//    use HasFactory;

    protected $table = 'sys_service_time';

    public function times()
    {
        return $this->hasMany($this, 'date', 'date');
    }
}