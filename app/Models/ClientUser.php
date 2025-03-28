<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientUser extends Model implements AuthenticatableContract
{
    use Authenticatable, SoftDeletes, HasFactory;

    /**
     * 与模型关联的表
     *
     * @var string
     */
    protected $table = 'client_users';

    /**
     * 模型属性的默认值
     *
     * @var array
     */
    protected $attributes = [
        'is_freeze' => false,
//        'is_del' => false,
//        'is_complete_info' => false,
        'remark' => null
    ];

    /**
     * 不是大量赋值的属性
     *
     * @var array
     */
    protected $guarded = [
        'deleted_at'
    ];

    /**
     * 应该始终被加载的关系
     *
     * @var string[]
     */
//    protected $with = ['info'];

    public function getAuthIdentifierName(): string
    {
        return 'uid';
    }

    // ==============================  闭包事件  ==============================
    protected static function booted(): void
    {
//        static::created(function (ClientUser $user) {
//            $user->info()->create([]);
//            $user->openIds()->createMany([
//                [
//                    'wechat_openid' => $user->last_wechat_openid,
//                    'wechat_unionid' => $user->last_wechat_unionid
//                ]
//            ]);
//        });
    }

    // ==============================  本地作用域  ==============================
    public function scopeCanLogin(Builder $query): void
    {
        $query->where(['is_freeze' => false, 'is_del' => false]);
    }

    // ==============================  关联  ==============================

    /**
     * 用户信息
     *
     * @return HasMany
     */
    public function info(): HasMany
    {
        return $this->hasMany(ClientUserInfo::class, 'user_id', 'id');
    }

    public function loginInfo(): HasMany
    {
        return $this->hasMany(ClientUserLoginInfo::class, 'user_id', 'id');
    }

//    public function deviceInfo(): HasMany
//    {
//        return $this->hasMany(ClientUserDeviceInfo::class, 'user_id', 'id');
//    }

    /**
     * 宠物列表
     *
     * @return HasMany
     */
    public function pets(): HasMany
    {
        return $this->hasMany(ClientUserPet::class, 'user_id', 'id')->chaperone();
    }

    /**
     * 服务地址列表
     *
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(ClientUserAddress::class, 'user_id', 'id')->chaperone();
    }

    /**
     * 优惠券列表
     *
     * @return HasMany
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(ClientUserCoupon::class, 'user_id', 'id')->chaperone();
    }

    /**
     * 订单列表
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(ClientUserOrder::class, 'user_id', 'id');
    }

    /**
     * 问卷调查
     *
     * @return HasOne
     */
    public function poll()
    {
        return $this->hasOne(ClientUserPoll::class, 'user_id', 'id');
    }
}
