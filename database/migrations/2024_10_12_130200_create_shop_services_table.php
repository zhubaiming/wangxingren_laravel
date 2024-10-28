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
        Schema::create('shop_services', function (Blueprint $table) {
            $table->id();
            $table->string('dict_id')->nullable(false)->comment('');
            $table->string('title')->nullable(false)->comment('');
            $table->foreignId('price')->nullable(false)->default(0)->comment('单价(单位: 分)');
            $table->foreignId('duration')->nullable(false)->default(0)->comment('持续时长(单位: 分钟)');
            $table->boolean('is_saling')->nullable(false)->default(true)->comment('是否上架: 0 - 否, 1 - 是');
            $table->boolean('is_del')->nullable(false)->default(false)->comment('是否删除: 0 - 否, 1 - 是');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_services');
    }
};
