<?php

namespace App\Models\Demo;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';

    protected $connection = 'mysql_demo';

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}