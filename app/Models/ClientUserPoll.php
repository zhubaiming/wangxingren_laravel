<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUserPoll extends Model
{
    use HasFactory;

    protected $table = 'client_user_poll';

    public $timestamps = false;

    protected $guarded = [];
}
