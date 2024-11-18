<?php

namespace App\Http\Resources\Wechat;

use Illuminate\Http\Request;

class GoodsServiceTimeCollection extends BaseCollection
{
    public $collects = GoodsServiceTimeResource::class;

    public function toArray(Request $request): array
    {
        $parent_result = parent::toArray($request);

        $result = [];

        foreach ($parent_result as $key => $value) {
            if (!isset($result[$value['date']])) {
                $result[$value['date']] = ['id' => $key, 'year' => $value['year'], 'month' => $value['month'], 'day' => $value['day'], 'title' => $value['title'], 'times' => []];
            }

            $result[$value['date']]['times'][] = ['id' => $value['id'], 'startTime' => $value['startTime'], 'endTime' => $value['endTime'], 'stock' => $value['stock'], 'timestamp' => strtotime($value['date'] . ' ' . $value['startTime'] . ':59'), 'duration' => $this->diffTimes($value['startTime'], $value['endTime'])];
        }

        return array_values($result);


//        if (isset($this->additional['self']) && $this->additional['self']) {
//            // 按日期分组 service_times 并格式化
//            $groupedTimes = $this->collection->groupBy(function ($item) {
//                return $item->date;
//            });
//
//            return $groupedTimes->map(function ($items, $date) {
//                $dateParts = explode('-', $date);
//
//                return [
//                    'year' => $dateParts[0],
//                    'month' => $dateParts[1],
//                    'day' => $dateParts[2],
//                    'title' => __('common.week.' . date('l', strtotime($date))) . "\n" . $dateParts[1] . '-' . $dateParts[2],
//                    'times' => GoodsServiceTimeResource::collection($items)
//                ];
//            })->values()->all();
//        } else {
//            return parent::toArray($request);
//        }
    }

    private function diffTimes($start_time, $end_time)
    {
        // 创建 DateTime 对象
        $start_date_time = new \DateTime($start_time);
        $end_date_time = new \DateTime($end_time);

        // 计算时间间隔
        $interval = $start_date_time->diff($end_date_time);

        // 转换为总分钟数
        return bcadd(bcmul($interval->h, '60', 0), $interval->i) . '分钟';
    }
}
