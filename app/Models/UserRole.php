<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';

    protected $fillable = ['title', 'can_delete', 'updated_by'];


    protected function casts(): array
    {
        return [
            'can_delete' => 'boolean',
        ];
    }

    // 关联 - 权限
    public function permissions()
    {
        return $this->belongsToMany(UserPermission::class, 'pivot_role_permission', 'role_id', 'permission_id', 'id', 'id');
    }

    // 关联 - 菜单
    public function menus()
    {
        return $this->belongsToMany(UserMenu::class, 'pivot_role_menu', 'role_id', 'menu_id', 'id', 'id');
    }
}
