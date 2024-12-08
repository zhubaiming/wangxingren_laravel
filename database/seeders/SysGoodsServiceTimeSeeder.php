<?php

namespace Database\Seeders;

use App\Models\SysGoodsServiceTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysGoodsServiceTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SysGoodsServiceTime::truncate();

        DB::statement('ALTER TABLE `sys_goods_service_time` AUTO_INCREMENT = 312');

        $date = date('Y-m-d');
        $inserts = [];
        for ($i = 0; $i < 8; $i++) {
            list('mday' => $mday, 'mon' => $mon, 'year' => $year) = getdate(strtotime("{$date} +{$i} day"));

            foreach (generateRandomTimeSlots(rand(10, 14)) as $times) {
                list($start_time, $end_time) = explode('-', $times);

                $_tmp = [];
                $_tmp['date'] = "{$year}-{$mon}-{$mday}";
//                $_tmp['stock'] = rand(0, 5);
                $_tmp['enable'] = true;

                $_tmp['start_time'] = $start_time;
                $_tmp['end_time'] = $end_time;

                $inserts[] = $_tmp;
            }
        }


        SysGoodsServiceTime::upsert($inserts, uniqueBy: ['id'], update: ['date', 'start_time', 'end_time']);
    }
}
