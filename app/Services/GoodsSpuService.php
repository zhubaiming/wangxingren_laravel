<?php

namespace App\Services;

use App\Models\SysDictionarie;
use App\Models\SysGoodsSpecGroup;
use Illuminate\Support\Facades\DB;

class GoodsSpuService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\SysGoodsSpu');

        $this->setTable('sys_goods_spu');

        $this->events = app('events');
    }

    public function createOne(array $spu, array $detail, array $service_times, array $spec_groups, array $skus)
    {
        DB::beginTransaction();

        try {
            $date_time = date('Y-m-d H:i:s');
            // 插入SPU
            $spu_id = DB::table('sys_goods_spu')->insertGetId($spu + ['created_at' => $date_time, 'updated_at' => $date_time]);

            // 插入Detail
            DB::table('sys_goods_spu_detail')->insert($detail + ['spu_id' => $spu_id, 'created_at' => $date_time, 'updated_at' => $date_time]);

            // 添加服务时间
            $service_times = array_map(function ($item) use ($spu_id) {
                $item['spu_id'] = $spu_id;
                return $item;
            }, $service_times);

            DB::table('sys_slot_goods_service_time_spu')->insert($service_times);

            // 插入SPU对应规格
            $spec_groups = array_map(function ($item) use ($spu_id) {
                return ['spec_group_id' => $item, 'spu_id' => $spu_id];
            }, $spec_groups);

            DB::table('sys_slot_goods_spec_group_spu')->insert($spec_groups);

            foreach ($skus as $sku) {
                $spec_groups = $sku['spec_groups'];
                unset($sku['spec_groups']);

                // 插入单一SPU
                $sku_id = DB::table('sys_goods_sku')->insertGetId($sku + ['spu_id' => $spu_id, 'created_at' => $date_time, 'updated_at' => $date_time]);

                // 插入sku对应规格的选值
                $spec_groups = array_map(function ($item) use ($sku_id) {
                    $sys_goods_spec_group = SysGoodsSpecGroup::find($item['spec_group_id']);
                    if ($sys_goods_spec_group) {
                        // 根据 spec_group_id 获取 taggable_type
                        $item['taggable_type'] = $this->getTaggableTypeForSysGoodsSpecGroup($sys_goods_spec_group);
                    }

                    $item['sku_id'] = $sku_id;

                    return $item;


                }, $spec_groups);

                DB::table('sys_slot_goods_sku_spec_group')->insert($spec_groups);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

//            throw $e;
            return false;
        }

        return true;
    }

    /**
     * 通过 spec_group_id 获取对应的 taggable_type，从数据字典表中查询
     * @param SysGoodsSpecGroup $specGroup
     * @return string
     */
    private function getTaggableTypeForSysGoodsSpecGroup(SysGoodsSpecGroup $specGroup): string
    {
        // 从数据字典表中获取 taggable_type
        $dict = SysDictionarie::where(['category' => 'sys_goods_spec_group', 'key' => $specGroup->id])->first();

        // 如果找不到匹配的 taggable_type，可以返回默认值
        return $dict ? $dict->value : 'DefaultTaggableType';
    }
}