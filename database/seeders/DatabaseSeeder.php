<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 货物品牌
            SysGoodsBrandSeeder::class,
            // 货物分类
            SysGoodsCategorySeeder::class,
            // 货物品牌与分类的关联表
            SysSlotGoodsCategoryBrandSeeder::class,
            // 货物参数组
            SysGoodsSpecGroupSeeder::class,
            // 货物SPU
            SysGoodsSpuSeeder::class,
            // 货物SPU详情
            SysGoodsSpuDetailSeeder::class,
            // 货物参数组与货物SPU的关联表
            SysSlotGoodsSpecGroupSpuSeeder::class,
//            SysGoodsSpecParamSeeder::class,
            // 宠物品种表
            SysPetBreedSeeder::class,
            // 货物SKU
            SysGoodsSkuSeeder::class,
        ]);
    }
}
