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
        /**
         * "id": 189,
         *  "version": 0,
         *  "type": 1,
         *  "dogSizeType": null,
         *  "name": "中国狸花猫",
         *  "code": "zhongguolihuamao",
         *  "reference": true,
         *  "createdBy": "mfd",
         *  "updatedBy": null,
         *   "picture": "https://test-hwc-gy1-bu-spaas-bucket-deepexi.obs.cn-southwest-2.myhuaweicloud.com/dm/console/317d_1652322174171-2022-05-12.jpeg",
         *   "remark": null,
         *
         * "tenantId": null,
         * "appId": null,
         * "deleted": 0,
         * "createdTime": "2022-05-12 10:22:59",
         * "updatedTime": "2024-10-14 10:33:15",
         * "hot": 0,
         * "lifeCycleId": 5,
         * "lifeCycleName": "猫咪通用生命周期",
         */
        Schema::create('sys_pets', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('version')->nullable(false)->default(0)->comment('');
            $table->unsignedTinyInteger('type')->nullable(false)->default(0)->comment('');
            $table->unsignedTinyInteger('sizeType')->nullable(true)->comment('');
            $table->string('name')->nullable(false)->comment('');
            $table->string('code')->nullable(false)->comment('');
            $table->boolean('reference')->nullable(false)->default(true)->comment('');
            $table->string('createdBy')->nullable(false)->default('sys')->comment('');
            $table->string('updatedBy')->nullable(true)->comment('');
            $table->longText('picture')->nullable(true)->comment('');
            $table->longText('remark')->nullable(true)->comment('备注信息');

            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['version']);
            $table->index(['type']);
            $table->index(['sizeType']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_pets');
    }
};
