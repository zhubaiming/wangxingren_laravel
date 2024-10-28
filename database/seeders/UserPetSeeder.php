<?php

namespace Database\Seeders;

use App\Models\Pet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_pets')->insert([
            [
                'user_id' => 1,
                'name' => '狗蛋',
                'type' => 2,
                'is_default' => true,
                'gender' => 1,
                'age' => 5,
                'breed' => '沙特尔',
                'color' => '灰色',
                'weight' => 6.23,
                'avatar' => 'https://img01.yzcdn.cn/vant/cat.jpeg',
                'remark' => null
            ],
            [
                'user_id' => 1,
                'name' => '雪糕',
                'type' => 2,
                'is_default' => false,
                'gender' => 1,
                'age' => 7,
                'breed' => '褴褛',
                'color' => '白色',
                'weight' => 8.11,
                'avatar' => 'https://img01.yzcdn.cn/vant/cat.jpeg',
                'remark' => null
            ],
            [
                'user_id' => 1,
                'name' => 'lucky',
                'type' => 1,
                'is_default' => false,
                'gender' => 1,
                'age' => 8,
                'breed' => '边牧',
                'color' => '棕色',
                'weight' => 23.62,
                'avatar' => 'https://img01.yzcdn.cn/vant/cat.jpeg',
                'remark' => null
            ],
            [
                'user_id' => 1,
                'name' => '妹妹',
                'type' => 1,
                'is_default' => false,
                'gender' => 2,
                'age' => 7,
                'breed' => '泰迪',
                'color' => '棕色',
                'weight' => 4.28,
                'avatar' => 'https://img01.yzcdn.cn/vant/cat.jpeg',
                'remark' => null
            ],
            [
                'user_id' => 1,
                'name' => '高富帅',
                'type' => 1,
                'is_default' => false,
                'gender' => 1,
                'age' => 10,
                'breed' => '太低',
                'color' => '白色',
                'weight' => 5.1,
                'avatar' => 'https://img01.yzcdn.cn/vant/cat.jpeg',
                'remark' => null
            ],
            [
                'user_id' => 1,
                'name' => '金蛋',
                'type' => 1,
                'is_default' => false,
                'gender' => 1,
                'age' => 13,
                'breed' => '比熊',
                'color' => '白色',
                'weight' => 7.88,
                'avatar' => 'https://img01.yzcdn.cn/vant/cat.jpeg',
                'remark' => null
            ]
        ]);
    }
}
