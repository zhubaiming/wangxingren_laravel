<?php

namespace Database\Seeders;

use App\Models\SysPetBreed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysPetBreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SysPetBreed::truncate();

        DB::statement('ALTER TABLE `sys_pet_breed` AUTO_INCREMENT = 618');

        SysPetBreed::upsert([
            ['type' => 1, 'title' => '阿比西尼亚', 'letter' => 'A'],
            ['type' => 1, 'title' => '布偶猫', 'letter' => 'B'],
            ['type' => 1, 'title' => '波斯猫', 'letter' => 'B'],
            ['type' => 1, 'title' => '长毛金渐层', 'letter' => 'C'],
            ['type' => 1, 'title' => '德文猫', 'letter' => 'D'],
            ['type' => 1, 'title' => '东方短毛猫', 'letter' => 'D'],
            ['type' => 1, 'title' => '金渐层', 'letter' => 'J'],
            ['type' => 1, 'title' => '金吉拉', 'letter' => 'J'],
            ['type' => 1, 'title' => '孟加拉豹猫', 'letter' => 'M'],
            ['type' => 1, 'title' => '美国短毛猫', 'letter' => 'M'],
            ['type' => 1, 'title' => '缅因猫', 'letter' => 'M'],
            ['type' => 1, 'title' => '无毛猫', 'letter' => 'W'],
            ['type' => 1, 'title' => '暹罗猫', 'letter' => 'X'],
            ['type' => 1, 'title' => '喜马拉雅猫', 'letter' => 'X'],
            ['type' => 1, 'title' => '蓝色英短', 'letter' => 'Y'],
            ['type' => 1, 'title' => '银渐层', 'letter' => 'Y'],

            ['type' => 2, 'title' => '阿拉斯加', 'letter' => 'A'],
            ['type' => 2, 'title' => '阿富汗', 'letter' => 'A'],
            ['type' => 2, 'title' => '比熊', 'letter' => 'B'],
            ['type' => 2, 'title' => '博美', 'letter' => 'B'],
            ['type' => 2, 'title' => '贝灵顿', 'letter' => 'B'],
            ['type' => 2, 'title' => '北京', 'letter' => 'B'],
            ['type' => 2, 'title' => '巴哥', 'letter' => 'B'],
            ['type' => 2, 'title' => '边牧', 'letter' => 'B'],
            ['type' => 2, 'title' => '巴吉度', 'letter' => 'B'],
            ['type' => 2, 'title' => '查理王', 'letter' => 'C'],
            ['type' => 2, 'title' => '柴犬', 'letter' => 'C'],
            ['type' => 2, 'title' => '德牧', 'letter' => 'D'],
            ['type' => 2, 'title' => '杜宾', 'letter' => 'D'],
            ['type' => 2, 'title' => '大麦町', 'letter' => 'D'],
            ['type' => 2, 'title' => '恶霸', 'letter' => 'E'],
            ['type' => 2, 'title' => '法牛', 'letter' => 'F'],
            ['type' => 2, 'title' => '古牧', 'letter' => 'G'],
            ['type' => 2, 'title' => '哈士奇', 'letter' => 'H'],
            ['type' => 2, 'title' => '吉娃娃', 'letter' => 'J'],
            ['type' => 2, 'title' => '金毛', 'letter' => 'J'],
            ['type' => 2, 'title' => '捷克狼', 'letter' => 'J'],
            ['type' => 2, 'title' => '巨贵', 'letter' => 'J'],
            ['type' => 2, 'title' => '柯基', 'letter' => 'K'],
            ['type' => 2, 'title' => '鹿犬', 'letter' => 'L'],
            ['type' => 2, 'title' => '拉布拉多', 'letter' => 'L'],
            ['type' => 2, 'title' => '玛尔济斯', 'letter' => 'M'],
            ['type' => 2, 'title' => '美卡', 'letter' => 'M'],
            ['type' => 2, 'title' => '秋田', 'letter' => 'Q'],
            ['type' => 2, 'title' => '苏格兰梗', 'letter' => 'S'],
            ['type' => 2, 'title' => '史宾格', 'letter' => 'S'],
            ['type' => 2, 'title' => '苏牧', 'letter' => 'S'],
            ['type' => 2, 'title' => '萨摩', 'letter' => 'S'],
            ['type' => 2, 'title' => '泰迪', 'letter' => 'T'],
            ['type' => 2, 'title' => '雪纳瑞', 'letter' => 'X'],
            ['type' => 2, 'title' => '西高地', 'letter' => 'X'],
            ['type' => 2, 'title' => '西施', 'letter' => 'X'],
            ['type' => 2, 'title' => '小可爱', 'letter' => 'X'],
            ['type' => 2, 'title' => '喜乐蒂', 'letter' => 'X'],
            ['type' => 2, 'title' => '约克夏', 'letter' => 'Y'],
            ['type' => 2, 'title' => '英斗', 'letter' => 'Y'],
            ['type' => 2, 'title' => '英卡', 'letter' => 'Y'],
            ['type' => 2, 'title' => '羊驼', 'letter' => 'Y']
        ], uniqueBy: ['id'], update: ['title']);
    }
}
