<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class GoodsServiceTimeResource extends CommentsResource
{
    protected function resourceData(): array
    {
        $dateParts = explode('-', $this->date);

        return [
            'id' => $this->id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'stock' => $this->pivot->stock,
            'year' => $dateParts[0],
            'month' => $dateParts[1],
            'day' => $dateParts[2],
            'title' => __('common.week.' . date('l', strtotime($this->date))) . "\n" . $dateParts[1] . '-' . $dateParts[2],
        ];
    }

    /*
     *  // 按日期分组 service_times 并格式化
            $groupedTimes = $this->collection->groupBy(function ($item) {
                return $item->date;
            });

            return $groupedTimes->map(function ($items, $date) {
                $dateParts = explode('-', $date);

                return [
                    'year' => $dateParts[0],
                    'month' => $dateParts[1],
                    'day' => $dateParts[2],
                    'title' => __('common.week.' . date('l', strtotime($date))) . "\n" . $dateParts[1] . '-' . $dateParts[2],
                    'times' => GoodsServiceTimeResource::collection($items)
                ];
            })->values()->all();
     */
}
