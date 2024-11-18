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
        Schema::create('dicts', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable(false)->comment('适用类型');
            $table->string('key')->nullable(false)->comment('键名');
            $table->string('value')->nullable(false)->comment('值');
            $table->string('value_local')->nullable(true)->comment('说明');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dicts');
    }
};
