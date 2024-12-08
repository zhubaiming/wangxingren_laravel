<?php

namespace Database\Seeders;

use App\Models\SysGoodsBrand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysGoodsBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SysGoodsBrand::truncate();

        DB::statement('ALTER TABLE `sys_goods_brand` AUTO_INCREMENT = 325403');

        SysGoodsBrand::create(['title' => '汪星人', 'letter' => 'W', 'image' => 'https://public-storages-bucket.oss-rg-china-mainland.aliyuncs.com/wangxingren/logo.png']);
    }
}
