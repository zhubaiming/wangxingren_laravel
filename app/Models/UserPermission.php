<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    public $timestamps = false;

//    protected $fillable = ['title', 'updated_by'];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // 监听 deleting 事件
        static::deleting(function ($item) {
            $item->roles()->detach();
            // 递归删除所有子级
            foreach ($item->children as $child) {
                $child->delete();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'select' => 'boolean',
        ];
    }

    protected function children()
    {
        return $this->hasMany($this, 'parent_id', 'id')->orderBy('sort', 'asc');
    }

    public function childrenRecursive()
    {
        return $this->children()->with(['childrenRecursive']);
    }

    // 关联 - 角色
    public function roles()
    {
        return $this->belongsToMany(UserRole::class, 'pivot_role_permission', 'permission_id', 'role_id', 'id', 'id');
    }
}
