<?php

namespace Database\Seeders;

use App\Models\SysGoodsSpu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysGoodsSpuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SysGoodsSpu::truncate();

        DB::statement('ALTER TABLE `sys_goods_spu` AUTO_INCREMENT = 187');

        SysGoodsSpu::upsert([
            ['title' => '伊莲娜经典系列', 'sub_title' => null, 'category_id' => 1426, 'brand_id' => 325403, 'saleable' => true],
            ['title' => 'PEK保湿松毛霜', 'sub_title' => null, 'category_id' => 1426, 'brand_id' => 325403, 'saleable' => true],
            ['title' => 'PH平衡护理素', 'sub_title' => null, 'category_id' => 1426, 'brand_id' => 325403, 'saleable' => true],
            ['title' => '普洗', 'sub_title' => null, 'category_id' => 1426, 'brand_id' => 325403, 'saleable' => true],
            ['title' => '精洗', 'sub_title' => null, 'category_id' => 1426, 'brand_id' => 325403, 'saleable' => true],
            ['title' => '美容', 'sub_title' => null, 'category_id' => 1426, 'brand_id' => 325403, 'saleable' => true],
            ['title' => '伊莲娜经典系列', 'sub_title' => null, 'category_id' => 1427, 'brand_id' => 325403, 'saleable' => true],
            ['title' => 'PEK保湿松毛霜', 'sub_title' => null, 'category_id' => 1427, 'brand_id' => 325403, 'saleable' => true],
            ['title' => 'PH平衡护理素', 'sub_title' => null, 'category_id' => 1427, 'brand_id' => 325403, 'saleable' => true],
            ['title' => '普洗', 'sub_title' => null, 'category_id' => 1427, 'brand_id' => 325403, 'saleable' => true],
            ['title' => '精洗', 'sub_title' => null, 'category_id' => 1427, 'brand_id' => 325403, 'saleable' => true],
            ['title' => '普洗+美容', 'sub_title' => null, 'category_id' => 1427, 'brand_id' => 325403, 'saleable' => true],
            ['title' => '精洗+美容', 'sub_title' => null, 'category_id' => 1427, 'brand_id' => 325403, 'saleable' => true]
        ], uniqueBy: ['id'], update: ['title']);
    }
}
