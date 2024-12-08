<?php

namespace App\Models\Demo;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $connection = 'mysql_demo';

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}