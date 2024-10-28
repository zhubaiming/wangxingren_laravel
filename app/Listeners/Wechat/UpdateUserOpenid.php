<?php

namespace App\Listeners\Wechat;

use App\Models\Wechat\UserIds;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserOpenid
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        /*
         *  $table->id();
            $table->foreignId('user_id');
            $table->string('wechat_openid', 50)->nullable(false)->comment('微信小程序用户唯一标识');
            $table->string('wechat_unionid', 50)->nullable(true)->comment('用户在开放平台的唯一标识符');
            $table->boolean('is_del')->nullable(false)->default(false)->comment('是否删除: 0 - 否, 1 - 是');
            $table->timestampsTz();
            $table->softDeletesTz();
         */

//        UserIds::firstOrCreate(
//            [
//                'user_id' => $user->id,
//                'wechat_openid' => $user->last_wechat_openid
//            ]
//        );

//        foreach ($user->loginInfo() as $login_info) {
//            $login_info->user_id
//        }
    }
}
