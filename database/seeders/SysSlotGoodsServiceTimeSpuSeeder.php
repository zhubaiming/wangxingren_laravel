<?php

namespace Database\Seeders;

use App\Models\SysGoodsServiceTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysSlotGoodsServiceTimeSpuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_slot_goods_service_time_spu')->truncate();

        $times = SysGoodsServiceTime::all();

        $inserts = [];

        foreach ($times as $time) {
            for ($i = 187; $i < 200; $i++) {
                $inserts[] = ['service_time_id' => $time->id, 'spu_id' => $i, 'stock' => rand(0, 2)];
            }
        }

        DB::table('sys_slot_goods_service_time_spu')->insert($inserts);
    }
}
