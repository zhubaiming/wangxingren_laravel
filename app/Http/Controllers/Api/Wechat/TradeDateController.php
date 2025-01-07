<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\SysTradeDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TradeDateController extends Controller
{
    public function getReservation(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        if (Carbon::parse($validated['date'])->lt(Carbon::today())) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '所选日期不允许预约');
        }

        if (0 === SysTradeDate::where('date', $validated['date'])->where('status', true)->count('id')) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '所选日期未营业，请重新选择');
        }

        try {
            $fullRange = ['start' => '09:00', 'end' => '19:00'];

            $times = [];
            for ($i = 0; $i < 2; $i++) {
                $times[$i] = ['car_number' => $i + 1, 'car_title' => ($i + 1) . ' 号车', 'times' => $this->getAvailableTimeRanges($fullRange, [], $validated['duration'])];
            }

//            ClientUserOrder::where('reservation_date', Carbon::parse($validated['date']))->latest('reservation_time_end')->firstOrFail();
        } catch (ModelNotFoundException) {

        }

        return $this->success(arrLineToHump($times));
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
            'start' => $this->timeToMinutes($fullRange['start']),
            'end' => $this->timeToMinutes($fullRange['end'])
        ];

        // 转换移除的时间段为分钟形式
        $removeRangesMinutes = array_map(function ($range) {
            return [
                'start' => $this->timeToMinutes($range['start']),
                'end' => $this->timeToMinutes($range['end'])
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
        $availableRanges = array_filter($remainingRanges, function ($range) use ($duration) {
            return ($range['end'] - $range['start']) >= $duration;
        });

        $newremainingRanges = [];
        foreach ($remainingRanges as $remainingRange) {
            $start = $remainingRange['start'];

            while ($start + $duration <= $remainingRange['end']) {
                $newremainingRanges[] = ['start' => $start, 'end' => $start + $duration];

                $start++;
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
