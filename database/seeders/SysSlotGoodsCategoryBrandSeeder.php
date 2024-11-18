<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysSlotGoodsCategoryBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_pivot_goods_category_brand')->truncate();

        DB::table('sys_pivot_goods_category_brand')->insert([
            ['category_id' => 1424, 'brand_id' => 325403],
            ['category_id' => 1425, 'brand_id' => 325403],
            ['category_id' => 1426, 'brand_id' => 325403],
            ['category_id' => 1427, 'brand_id' => 325403]
        ]);
    }
}