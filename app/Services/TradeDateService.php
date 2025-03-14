<?php

namespace App\Services;

class TradeDateService
{
    private function timeToMinutes($time)
    {
        [$hour, $minute] = explode(':', $time);

        return $hour * 60 + ($minute * 1);
    }

    private function formatTime($minutes)
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private function transformRanges($unitary, $full)
    {
        return [
            // 将开始时间和结束时转为分钟形式
            [
                'start' => $this->timeToMinutes($unitary['start']),
                'end' => $this->timeToMinutes($unitary['end'])
            ],
            // 转换移除的时间段为分钟形式
            array_map(function ($range) {
                return [
                    'start' => $this->timeToMinutes($range['start']),
                    'end' => $this->timeToMinutes($range['end'])
                ];
            }, $full)
        ];
    }

    public function getAvailableTimeRanges(array $fullRange, array $removeRanges, int $duration, $intervals = 15)
    {
        [$fullRangeMinutes, $removeRangesMinutes] = $this->transformRanges($fullRange, $removeRanges);

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

        $newremainingRanges = [];
        foreach ($remainingRanges as $remainingRange) {
            $start = $remainingRange['start'];

            while ($start + $duration <= $remainingRange['end']) {
                $newremainingRanges[] = ['start' => $start, 'end' => $start + $duration];

                $start += $intervals;
            }
        }

        // 将结果转换回时间格式
        return array_map(function ($range) {
            return [
                'start' => $this->formatTime($range['start']),
                'end' => $this->formatTime($range['end'])
            ];
        }, $newremainingRanges);
    }

    public function checkTimeRange(array $checkRange, array $allRanges): bool
    {
        [$checkRangeMinutes, $allRangesMinutes] = $this->transformRanges($checkRange, $allRanges);

        $result = true;
        foreach ($allRangesMinutes as $minute) {
            if ($checkRangeMinutes['end'] <= $minute['start'] || $minute['end'] <= $checkRangeMinutes['start']) {
                continue;
            } else {
                $result = false;
                break;
            }
        }

        return $result;
    }
}