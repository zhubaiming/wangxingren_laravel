<?php

namespace Database\Seeders;

use App\Models\SysPetBreedWeight;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysPetBreedWeightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SysPetBreedWeight::truncate();

        DB::statement('ALTER TABLE `sys_pet_breed_weight` AUTO_INCREMENT = 1111');

        $data = collect([
            ['breed_id' => 634, 'min' => 0.00, 'max' => 20.00, 'title' => '1 - 20kg'],
            ['breed_id' => 634, 'min' => 20.00, 'max' => 999.99, 'title' => '20kg以上'],
            ['breed_id' => 635, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 636, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 636, 'min' => 5.00, 'max' => 7.50, 'title' => '5 - 7.5公斤'],
            ['breed_id' => 636, 'min' => 7.50, 'max' => 10.00, 'title' => '7.5 - 10公斤'],
            ['breed_id' => 637, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 637, 'min' => 5.00, 'max' => 7.50, 'title' => '5 - 7.5公斤'],
            ['breed_id' => 637, 'min' => 7.50, 'max' => 10.00, 'title' => '7.5 - 10公斤'],
            ['breed_id' => 638, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 638, 'min' => 5.00, 'max' => 7.50, 'title' => '5 - 7.5公斤'],
            ['breed_id' => 638, 'min' => 7.50, 'max' => 10.00, 'title' => '7.5 - 10公斤'],
            ['breed_id' => 638, 'min' => 10.00, 'max' => 15.00, 'title' => '10 - 15公斤'],
            ['breed_id' => 639, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 639, 'min' => 5.00, 'max' => 10.00, 'title' => '5 - 10公斤'],
            ['breed_id' => 640, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 640, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 641, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 641, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 642, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 642, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 643, 'min' => 0.00, 'max' => 999.99, 'title' => '全体重'],
            ['breed_id' => 644, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 644, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 645, 'min' => 0.00, 'max' => 20.00, 'title' => '1 - 20kg'],
            ['breed_id' => 645, 'min' => 20.00, 'max' => 999.99, 'title' => '20kg以上'],
            ['breed_id' => 646, 'min' => 0.00, 'max' => 20.00, 'title' => '1 - 20kg'],
            ['breed_id' => 646, 'min' => 20.00, 'max' => 999.99, 'title' => '20kg以上'],
            ['breed_id' => 647, 'min' => 0.00, 'max' => 20.00, 'title' => '1 - 20kg'],
            ['breed_id' => 647, 'min' => 20.00, 'max' => 999.99, 'title' => '20kg以上'],
            ['breed_id' => 648, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 648, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 649, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 649, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 650, 'min' => 0.00, 'max' => 20.00, 'title' => '1 - 20kg'],
            ['breed_id' => 650, 'min' => 20.00, 'max' => 999.99, 'title' => '20kg以上'],
            ['breed_id' => 651, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 651, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 652, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 652, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 653, 'min' => 0.00, 'max' => 20.00, 'title' => '1 - 20kg'],
            ['breed_id' => 653, 'min' => 20.00, 'max' => 999.99, 'title' => '20kg以上'],
            ['breed_id' => 654, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 654, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 655, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 656, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 656, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 657, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 657, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 658, 'min' => 0.00, 'max' => 20.00, 'title' => '1 - 20kg'],
            ['breed_id' => 658, 'min' => 20.00, 'max' => 999.99, 'title' => '20kg以上'],
            ['breed_id' => 659, 'min' => 0.00, 'max' => 999.99, 'title' => '全体重'],
            ['breed_id' => 660, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 660, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 661, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 661, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 662, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 662, 'min' => 5.00, 'max' => 7.50, 'title' => '5 - 7.5公斤'],
            ['breed_id' => 662, 'min' => 7.50, 'max' => 10.00, 'title' => '7.5 - 10公斤'],
            ['breed_id' => 662, 'min' => 10.00, 'max' => 15.00, 'title' => '10 - 15公斤'],
            ['breed_id' => 663, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 663, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 664, 'min' => 0.00, 'max' => 20.00, 'title' => '1 - 20kg'],
            ['breed_id' => 664, 'min' => 20.00, 'max' => 999.99, 'title' => '20kg以上'],
            ['breed_id' => 665, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 665, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 666, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 666, 'min' => 5.00, 'max' => 7.50, 'title' => '5 - 7.5公斤'],
            ['breed_id' => 666, 'min' => 7.50, 'max' => 10.00, 'title' => '7.5 - 10公斤'],
            ['breed_id' => 667, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 667, 'min' => 5.00, 'max' => 7.50, 'title' => '5 - 7.5公斤'],
            ['breed_id' => 667, 'min' => 7.50, 'max' => 10.00, 'title' => '7.5 - 10公斤'],
            ['breed_id' => 667, 'min' => 10.00, 'max' => 15.00, 'title' => '10 - 15公斤'],
            ['breed_id' => 668, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 668, 'min' => 5.00, 'max' => 7.50, 'title' => '5 - 7.5公斤'],
            ['breed_id' => 668, 'min' => 7.50, 'max' => 10.00, 'title' => '7.5 - 10公斤'],
            ['breed_id' => 668, 'min' => 10.00, 'max' => 15.00, 'title' => '10 - 15公斤'],
            ['breed_id' => 669, 'min' => 0.00, 'max' => 5.00, 'title' => '1 - 5公斤'],
            ['breed_id' => 669, 'min' => 5.00, 'max' => 10.00, 'title' => '5 - 10公斤'],
            ['breed_id' => 670, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 670, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 671, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 671, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 672, 'min' => 0.00, 'max' => 999.99, 'title' => '全体重'],
            ['breed_id' => 673, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 673, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上'],
            ['breed_id' => 674, 'min' => 0.00, 'max' => 10.00, 'title' => '1 - 10公斤'],
            ['breed_id' => 674, 'min' => 10.00, 'max' => 999.99, 'title' => '10kg以上'],
            ['breed_id' => 675, 'min' => 0.00, 'max' => 15.00, 'title' => '1 - 15公斤'],
            ['breed_id' => 675, 'min' => 15.00, 'max' => 999.99, 'title' => '15kg以上']
        ])->map(function ($item) {
            $item['min'] = applyFloatToIntegerModifier($item['min']);
            $item['max'] = applyFloatToIntegerModifier($item['max']);
            return $item;
        })->toArray();

        SysPetBreedWeight::upsert($data, uniqueBy: ['breed_id', 'min', 'max'], update: ['title']);
    }
}
