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
        Schema::create('sys_goods_category', function (Blueprint $table) {
            $table->id();
            $table->string('title', 32)->nullable(false)->comment('类目名称');
            $table->foreignId('parent_id')->nullable(false)->default(0)->comment('父类目id, 顶级类目填0');
            $table->unsignedTinyInteger('is_parent')->nullable(false)->default(true)->comment('是否为父节点: 0 - 否, 1 - 是');
            $table->unsignedTinyInteger('sort')->nullable(false)->default()->comment();
            $table->foreignId('')->nullable(false)->default()->comment();

            $table->timestamps();

//            $table->id();
//            $table->foreignId('user_login_info_id')->nullable(true)->comment('登录微信id');
//            $table->string('phone_number', 16)->nullable(true)->comment('手机号码');
//            $table->string('phone_prefix', 5)->nullable(true)->comment('手机号国别前缀');
//            $table->string('nick_name', 50)->nullable(true)->comment('昵称');
//            $table->string('avatar', 255)->nullable(true)->comment('头像');
//            $table->string('name', 50)->nullable(true)->comment('姓名');
//            $table->string('card_no', 32)->nullable(true)->comment('身份证号码');
//            $table->unsignedTinyInteger('gender')->nullable(true)->default(0)->comment('性别');
//            $table->date('birthday')->nullable(true)->comment('出生日期');
//            $table->unsignedTinyInteger('birth_month')->nullable(true)->comment('生日 - 月');
//            $table->unsignedTinyInteger('birth_day')->nullable(true)->comment('生日 - 日');
//            $table->longText('remark')->nullable(true)->comment('备注信息');
//            $table->ipAddress('last_login_ip')->nullable(true)->comment('最后登录ip地址');
//            $table->boolean('is_login')->nullable(false)->default(true)->comment('是否登录: 0 - 否, 1 - 是');
//            $table->boolean('is_freeze')->nullable(false)->default(false)->comment('是否冻结: 0 - 否, 1 - 是');

//            $table->timestampsTz();
//            $table->softDeletesTz();
//
////            $table->unique('phone_number');
//            $table->index(['phone_number']);
//            $table->index(['name']);
//            $table->index(['card_no']);
//            $table->index(['birthday']);
//            $table->index(['birth_month']);
//            $table->index(['birth_day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_goods_category');
    }
};
