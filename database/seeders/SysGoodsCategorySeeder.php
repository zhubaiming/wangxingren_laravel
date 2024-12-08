<?php

namespace Database\Seeders;

use App\Models\SysGoodsCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysGoodsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SysGoodsCategory::truncate();

        DB::statement('ALTER TABLE `sys_goods_category` AUTO_INCREMENT = 1424');

        SysGoodsCategory::upsert([
            ['title' => '服务型商品', 'parent_id' => 0, 'is_parent' => 1, 'sort' => 1],
            ['title' => '销售型商品', 'parent_id' => 0, 'is_parent' => 1, 'sort' => 2],
            ['title' => '宠物猫服务', 'parent_id' => 1424, 'is_parent' => 0, 'sort' => 1],
            ['title' => '宠物狗服务', 'parent_id' => 1424, 'is_parent' => 0, 'sort' => 2],
//            ['title' => '伊珊娜经典系列', 'parent_id' => 3, 'is_parent' => 0, 'sort' => 1],
//            ['title' => 'PEK保湿松毛霜', 'parent_id' => 3, 'is_parent' => 0, 'sort' => 2],
//            ['title' => 'PH平衡护理素', 'parent_id' => 3, 'is_parent' => 0, 'sort' => 3],
//            ['title' => '普洗', 'parent_id' => 3, 'is_parent' => 0, 'sort' => 4],
//            ['title' => '精洗', 'parent_id' => 3, 'is_parent' => 0, 'sort' => 5],
//            ['title' => '美容', 'parent_id' => 3, 'is_parent' => 0, 'sort' => 6],
//            ['title' => '伊珊娜经典系列', 'parent_id' => 4, 'is_parent' => 0, 'sort' => 1],
//            ['title' => 'PEK保湿松毛霜', 'parent_id' => 4, 'is_parent' => 0, 'sort' => 2],
//            ['title' => 'PH平衡护理素', 'parent_id' => 4, 'is_parent' => 0, 'sort' => 3],
//            ['title' => '普洗', 'parent_id' => 4, 'is_parent' => 0, 'sort' => 4],
//            ['title' => '精洗', 'parent_id' => 4, 'is_parent' => 0, 'sort' => 5],
//            ['title' => '普洗+美容', 'parent_id' => 4, 'is_parent' => 0, 'sort' => 6],
//            ['title' => '精洗+美容', 'parent_id' => 4, 'is_parent' => 0, 'sort' => 7]
        ], uniqueBy: ['id'], update: ['title']);
    }
}
