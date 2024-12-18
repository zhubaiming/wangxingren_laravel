<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
//        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'account' => 'string',
            'phone_number' => 'string',
            'status' => 'boolean',
            'is_default_passwd' => 'boolean',
            'can_delete' => 'boolean',
//            'email_verified_at' => 'datetime',
//            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id', 'id');
    }
}
