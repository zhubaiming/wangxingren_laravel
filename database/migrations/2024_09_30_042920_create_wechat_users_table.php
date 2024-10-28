<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_login_info_id')->nullable(true)->comment('登录微信id');
            $table->string('phone_number', 16)->nullable(true)->comment('手机号码');
            $table->string('phone_prefix', 5)->nullable(true)->comment('手机号国别前缀');
            $table->string('nick_name', 50)->nullable(true)->comment('昵称');
            $table->string('avatar', 255)->nullable(true)->comment('头像');
            $table->string('name', 50)->nullable(true)->comment('姓名');
            $table->string('card_no', 32)->nullable(true)->comment('身份证号码');
            $table->unsignedTinyInteger('gender')->nullable(true)->default(0)->comment('性别');
            $table->date('birthday')->nullable(true)->comment('出生日期');
            $table->unsignedTinyInteger('birth_month')->nullable(true)->comment('生日 - 月');
            $table->unsignedTinyInteger('birth_day')->nullable(true)->comment('生日 - 日');
            $table->longText('remark')->nullable(true)->comment('备注信息');
            $table->ipAddress('last_login_ip')->nullable(true)->comment('最后登录ip地址');
            $table->boolean('is_login')->nullable(false)->default(true)->comment('是否登录: 0 - 否, 1 - 是');
            $table->boolean('is_freeze')->nullable(false)->default(false)->comment('是否冻结: 0 - 否, 1 - 是');

            $table->timestampsTz();
            $table->softDeletesTz();

//            $table->unique('phone_number');
            $table->index(['phone_number']);
            $table->index(['name']);
            $table->index(['card_no']);
            $table->index(['birthday']);
            $table->index(['birth_month']);
            $table->index(['birth_day']);

        });

        Schema::create('client_user_login_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(true)->comment('用户id');
            $table->foreignId('user_device_info_id')->nullable(true)->comment('使用设备id');
            $table->string('app_type', 50)->nullable(false)->comment('APP类型');
            $table->string('appid', 50)->nullable(false)->comment('APP标识');
            $table->string('wechat_openid', 50)->nullable(false)->comment('微信小程序用户唯一标识');
            $table->string('wechat_unionid', 50)->nullable(true)->comment('用户在开放平台的唯一标识符');
            $table->boolean('is_register')->nullable(false)->default(false)->comment('是否注册: 0 - 否, 1 - 是');
            $table->timestampsTz();

            $table->unique(['user_id', 'app_type', 'appid', 'wechat_openid'], 'joint_unique');
            $table->index(['app_type', 'appid', 'wechat_openid'], 'joint_index');
            $table->index(['app_type']);
            $table->index(['appid']);
        });

        Schema::create('client_users_device_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_login_info_id')->nullable(false)->comment('登录微信id');
            $table->string('brand')->nullable(false)->comment('设备品牌');
            $table->string('model')->nullable(false)->comment('设备型号，新机型刚推出一段时间会显示unknown，微信会尽快进行适配');
            $table->string('system')->nullable(false)->comment('操作系统及版本');
            $table->string('platform')->nullable(false)->comment('客户端平台');
            $table->string('memory_size')->nullable(false)->comment('设备内存大小，单位为 MB');
            $table->string('SDK_version')->nullable(false)->comment('客户端基础库版本');
            $table->string('language')->nullable(false)->comment('微信设置的语言');
            $table->string('version')->nullable(false)->comment('微信版本号');
            $table->string('theme')->nullable(true)->comment('系统当前主题，取值为`light`或`dark`，全局配置`"darkmode":true`时才能获取，否则为 undefined ');
            $table->unsignedTinyInteger('font_size_scale_factor')->nullable(false)->comment('微信字体大小缩放比例');
            $table->unsignedTinyInteger('font_size_setting')->nullable(false)->comment('微信字体大小，单位px');
            $table->timestampsTz();

            $table->unique(['user_login_info_id', 'brand', 'model', 'system'], 'joint_unique');
            $table->index(['user_login_info_id', 'brand', 'model', 'system'], 'joint_index');
            $table->index(['brand']);
            $table->index(['model']);
            $table->index(['system']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_users');
        Schema::dropIfExists('client_user_login_infos');
        Schema::dropIfExists('client_users_device_infos');
    }
};
