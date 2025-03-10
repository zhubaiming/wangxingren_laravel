<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Enums\OrderStatusEnum;
use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\ClientUserOrder;
use App\Models\ServiceCar;
use App\Models\System;
use App\Models\SysTradeDate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TradeDateController extends Controller
{
    public function getReservation(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        $now = Carbon::now()->addMinutes(30);
        $date = Carbon::createFromTimeStamp($validated['date'] / 1000, config('app.timezone'));
//        $date = Carbon::parse($validated['date'], config('app.timezone'));

        if ($date->lt(Carbon::today())) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '不可选择今天之前的日期');
        }

        if (0 === SysTradeDate::where('date', $date->format('Y-m-d'))->where('status', true)->count('id')) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '所选日期未营业，请重新选择');
        }

        $fullRange = json_decode(System::where('key', 'COMPANY_TRADE_TIMES')->firstOrFail()->value, true);

        $times = [];
        $cars = ServiceCar::select('id', 'title')->where('status', true)->orderBy('created_at', 'asc')->get();

        if (0 !== count($cars)) {
            $orders = ClientUserOrder::select('reservation_car', 'reservation_time_start', 'reservation_time_end')->whereIn('status', [OrderStatusEnum::finishing, OrderStatusEnum::finished, OrderStatusEnum::refund])->where('reservation_date', $date->format('Y-m-d'))->get()->toArray();

            // 使用 array_reduce 实现分组
            $removeRanges = array_reduce($orders, function ($carry, $item) {
                $carId = $item['reservation_car']; // 获取分组的键
                $carry[$carId][] = $item;         // 将当前元素追加到对应分组中
                return $carry;                    // 返回累积的分组结果
            }, []);
        }

        foreach ($cars as $car) {
            if ($date->isToday()) {
                $removeRanges[$car->id][] = ['reservation_car' => $car->id, 'reservation_time_start' => $fullRange['time_start'], 'reservation_time_end' => $now->hour . ':' . $now->minute];
            }
            $times[$car->id] = ['car_number' => $car->id, 'car_title' => $car->title, 'times' => $this->getAvailableTimeRanges($fullRange, $removeRanges[$car->id] ?? [], $validated['duration'])];
        }

        return $this->success(arrLineToHump(array_values($times)));
    }

    // 时间转分钟工具函数
    private function timeToMinutes($time): int
    {
        [$hour, $minute] = explode(':', $time);

        return $hour * 60 + ($minute * 1);
    }

    // 分钟转时间工具函数
    private function formatTime(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private function getAvailableTimeRanges($fullRange, $removeRanges, $duration)
    {
        // 将开始时间和结束时转为分钟形式
        $fullRangeMinutes = [
            'start' => $this->timeToMinutes($fullRange['time_start']),
            'end' => $this->timeToMinutes($fullRange['time_end'])
        ];

        // 转换移除的时间段为分钟形式
        $removeRangesMinutes = array_map(function ($range) {
            return [
                'start' => $this->timeToMinutes($range['reservation_time_start']),
                'end' => $this->timeToMinutes($range['reservation_time_end'])
            ];
        }, $removeRanges);

        // 计算剩余时间段
        $remainingRanges = [$fullRangeMinutes];
        foreach ($removeRangesMinutes as $remove) {
            $newRemaining = [];
            foreach ($remainingRanges as $range) {
                // 如果当前范围与移除范围无交集，保留
                if ($range['end'] <= $remove['start'] || $range['start'] >= $remove['end']) {
                    $newRemaining[] = $range;
                } else {
                    // 存在交集，可能需要分裂时间段
                    if ($range['start'] < $remove['start']) {
                        $newRemaining[] = [
                            'start' => $range['start'],
                            'end' => $remove['start'],
                        ];
                    }
                    if ($range['end'] > $remove['end']) {
                        $newRemaining[] = [
                            'start' => $remove['end'],
                            'end' => $range['end'],
                        ];
                    }
                }
            }
            $remainingRanges = $newRemaining;
        }

        // 过滤出能够覆盖指定时长的时间段
//        $availableRanges = array_filter($remainingRanges, function ($range) use ($duration) {
//            return ($range['end'] - $range['start']) >= $duration;
//        });

        $newremainingRanges = [];
        foreach ($remainingRanges as $remainingRange) {
            $start = $remainingRange['start'];

            while ($start + $duration <= $remainingRange['end']) {
                $newremainingRanges[] = ['start' => $start, 'end' => $start + $duration];

//                $start++;
                $start += 15;
            }
        }

        // 将结果转换回时间格式
        return array_map(function ($range) use ($duration) {
            return [
                'start' => $this->formatTime($range['start']),
                'end' => $this->formatTime($range['end'])
//                'latest_start' => $this->formatTime($range['end'] - $duration), // 最晚可开始时间
            ];
        }, $newremainingRanges);
//        }, $availableRanges);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
