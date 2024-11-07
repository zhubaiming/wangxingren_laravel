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
function arrHumpToLine(array $arr)
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
function arrLineToHump(array $arr)
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