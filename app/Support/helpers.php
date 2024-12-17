<?php

use Illuminate\Support\Str;

/**
 * 根据提供的日期计算年龄
 *
 * @param $birth
 * @param $format
 * @return int|string
 */
function calculateAge($birth, $format)
{
    // 将输入的年份和月份字符串转换为 DateTime 对象
    $birthDate = DateTime::createFromFormat($format, $birth);

    // 检查日期是否有效
    if ($birthDate === false) {
        return '无效的日期格式';
    }

    // 获取当前日期
    $currentDate = new DateTime();

    // 计算年龄
    $age = $currentDate->diff($birthDate)->y;

    return $age;
}

/**
 * 数组 key 驼峰转蛇形
 *
 * @param array $arr
 * @return array
 */
function arrHumpToLine(array $arr): array
{
    /**
     * $converted = Str::snake('fooBar');
     *
     * // foo_bar
     */
    $keys = [];

    foreach ($arr as $key => $value) {
        $keys[] = Str::snake($key);
    }

    return array_combine($keys, $arr);
}

/**
 * 数组 key 蛇形转驼峰
 *
 * @param array $arr
 * @return array
 */
function arrLineToHump(array $arr): array
{
    /**
     * $converted = Str::camel('foo_bar');
     *
     * // 'fooBar'
     */
    $keys = [];

    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $arr[$key] = arrLineToHump($value);
        }

        $keys[] = Str::camel($key);
    }

    return array_combine($keys, $arr);
}

function applyFloatToIntegerModifier(float|string $value, string|int $mul = '100', $scale = 0)
{
    return intval(bcmul($value, $mul, $scale));
}

function applyIntegerToFloatModifier(int $value, string|int $mul = '100', int $scale = 2)
{
    return bcdiv($value, $mul, $scale);
}

function generateLuhnCheckDigit($number)
{
    $number = strrev($number);  // 将数字反转，便于从右至左处理
    $sum = 0;

    // 遍历数字的每一位
    for ($i = 0; $i < strlen($number); $i++) {
        $digit = (int)$number[$i];  // 当前数字

        // 如果是偶数位（从右侧开始计数），乘以 2
        if ($i % 2 == 1) {
            $digit *= 2;
            // 如果结果大于 9，需要对结果的各位数字求和
            if ($digit > 9) {
                $digit -= 9;  // 即：9 + (digit - 10)
            }
        }

        $sum += $digit;  // 将当前数字加到总和
    }

    // 计算校验位
    $checkDigit = (10 - ($sum % 10)) % 10;
    return $checkDigit;
}

function luhnCheck($number)
{
    $number = strrev($number);  // 将数字反转，便于从右至左处理
    $sum = 0;

    // 遍历数字的每一位
    for ($i = 0; $i < strlen($number); $i++) {
        $digit = (int)$number[$i];  // 当前数字

        // 如果是偶数位（从右侧开始计数），乘以 2
        if ($i % 2 == 1) {
            $digit *= 2;
            // 如果结果大于 9，需要对结果的各位数字求和
            if ($digit > 9) {
                $digit -= 9;  // 即：9 + (digit - 10)
            }
        }

        $sum += $digit;  // 将当前数字加到总和
    }

    // 如果总和能被 10 整除，则验证通过
    return $sum % 10 == 0;
}

function isTrue($value, $return_null = false)
{
    $boolval = (is_string($value) ? filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool)$value);

    return ($boolval === null && $return_null ? false : $boolval);
}

// --------------------------------------------------  以下为测试专用函数  --------------------------------------------------
/**
 * 随机生成8:00-22:00中的几个时间段
 * @param $numSlots
 * @return array
 * @throws DateMalformedIntervalStringException
 */
function generateRandomTimeSlots($numSlots)
{
    $start = new DateTime('08:00');
    $end = new DateTime('22:00');
    $interval = new DateInterval('PT30M'); // 30 分钟间隔
    $allSlots = [];

    // 生成所有可能的 30 分钟间隔的时间段
    while ($start < $end) {
        $next = clone $start;
        $next->add($interval);
        $allSlots[] = [$start->format('H:i'), $next->format('H:i')];
        $start = $next;
    }

    $selectedSlots = [];

    // 确保第一个时间段从 08:00 开始
    $initialLength = rand(1, min(6, count($allSlots) - $numSlots)); // 随机选择第一个时间段长度
    $firstEndIndex = $initialLength - 1;
    $selectedSlots[] = $allSlots[0][0] . '-' . $allSlots[$firstEndIndex][1];

    $i = $firstEndIndex + 1;

    // 生成中间的随机时间段
    while ($i < count($allSlots) && count($selectedSlots) < $numSlots - 1) {
        $remainingSlots = count($allSlots) - $i - ($numSlots - count($selectedSlots) - 1);
        $slotLength = rand(1, min(6, $remainingSlots)); // 确保不会超出数组范围
        $endIndex = $i + $slotLength - 1;

        $startTime = $allSlots[$i][0];
        $endTime = $allSlots[$endIndex][1];

        $selectedSlots[] = "$startTime-$endTime";
        $i = $endIndex + 1;
    }

    // 确保最后一个时间段以 22:00 结束
    $lastStartIndex = $i;
    $selectedSlots[] = $allSlots[$lastStartIndex][0] . '-' . $allSlots[count($allSlots) - 1][1];

    return $selectedSlots;
}