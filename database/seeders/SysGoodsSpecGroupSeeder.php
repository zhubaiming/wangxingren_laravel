<?php

namespace Database\Seeders;

use App\Models\SysGoodsSpecGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysGoodsSpecGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SysGoodsSpecGroup::truncate();

        DB::statement('ALTER TABLE `sys_goods_spec_group` AUTO_INCREMENT = 15');

        SysGoodsSpecGroup::upsert([
            ['category_id' => 1426, 'title' => '适用宠物品种'],
            ['category_id' => 1427, 'title' => '适用宠物品种'],
        ], uniqueBy: ['id'], update: ['title']);
    }
}