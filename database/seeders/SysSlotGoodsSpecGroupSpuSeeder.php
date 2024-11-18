<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysSlotGoodsSpecGroupSpuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_slot_goods_spec_group_spu')->truncate();

        DB::table('sys_slot_goods_spec_group_spu')->insert([ // 15-17
            ['spec_group_id' => 15, 'spu_id' => 187],
            ['spec_group_id' => 15, 'spu_id' => 188],
            ['spec_group_id' => 15, 'spu_id' => 189],
            ['spec_group_id' => 15, 'spu_id' => 190],
            ['spec_group_id' => 15, 'spu_id' => 191],
            ['spec_group_id' => 15, 'spu_id' => 192],
            ['spec_group_id' => 16, 'spu_id' => 193],
            ['spec_group_id' => 16, 'spu_id' => 194],
            ['spec_group_id' => 16, 'spu_id' => 195],
            ['spec_group_id' => 16, 'spu_id' => 196],
            ['spec_group_id' => 16, 'spu_id' => 197],
            ['spec_group_id' => 16, 'spu_id' => 198],
            ['spec_group_id' => 16, 'spu_id' => 199],
            ['spec_group_id' => 17, 'spu_id' => 193],
            ['spec_group_id' => 17, 'spu_id' => 194],
            ['spec_group_id' => 17, 'spu_id' => 195],
            ['spec_group_id' => 17, 'spu_id' => 196],
            ['spec_group_id' => 17, 'spu_id' => 197],
            ['spec_group_id' => 17, 'spu_id' => 198],
            ['spec_group_id' => 17, 'spu_id' => 199],
        ]);
    }
}
