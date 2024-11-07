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
        Schema::create('user_pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(false)->comment('用户id');
            $table->foreignId('variety_id')->nullable(false)->comment('品种id');
            $table->string('variety_name')->nullable(false)->comment('品种名称');

            $table->string('name', 50)->nullable(false)->comment('宠物名称/昵称');
            $table->unsignedTinyInteger('category')->nullable(false)->comment('宠物类型');
            $table->unsignedTinyInteger('gender')->nullable(false)->default(0)->comment('宠物性别');
            $table->unsignedSmallInteger('weight')->nullable(false)->comment('宠物体重(公斤), 无小数');
            $table->unsignedTinyInteger('weight_type')->nullable(false)->comment('宠物体重属性');
            $table->string('birth', 7)->nullable(true)->comment('出生年月');
            $table->unsignedTinyInteger('age')->nullable(true)->comment('宠物年龄');
            $table->string('color', 100)->nullable(true)->comment('宠物颜色');

            $table->string('avatar')->nullable(true)->comment('宠物头像');
            $table->longText('remark')->nullable(true)->comment('备注');

            $table->boolean('is_sterilization')->nullable(true)->comment('是否绝育: 0 - 否, 1 - 是');
            $table->boolean('is_default')->nullable(false)->default(false)->comment('是否默认: 0 - 否, 1 - 是');

            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index('user_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_pets');
    }
};
