<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMenu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected function casts(): array
    {
        return [
            'is_parent' => 'boolean',
//            'email_verified_at' => 'datetime',
//            'password' => 'hashed',
        ];
    }

    private function children()
    {
        return $this->hasMany($this, 'parent_id', 'id')->orderBy('sort', 'asc');
    }

    public function childrenRecursive()
    {
        return $this->children()->with(['childrenRecursive']);
    }

    private function parent()
    {
        return $this->hasOne($this,'id','parent_id');
    }

    public function parentRecursive()
    {
        return $this->parent()->with(['parentRecursive']);
    }
}
