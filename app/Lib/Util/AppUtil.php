<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/26
 * Time: 18:03
 */

/**
 * Class AppUtil
 */
class AppUtil
{
    static function getEndDateByTimezone($endDate, $timezone)
    {
        return strtotime('+1 day -1 sec', strtotime($endDate)) - $timezone * HOUR;
    }

    /**
     * timezoneを考慮したtimestampを返す
     *
     * @param  string $dateStr
     * @param  float  $timezone
     *
     * @return int
     */
    static function getTimestampByTimezone(string $dateStr, float $timezone): int
    {
        return strtotime($dateStr) - $timezone * HOUR;
    }

    /**
     * 配列か判定(連想配列はfalse)
     * メモリ増加を防ぐ為参照渡しとする
     *
     * @param $ar
     *
     * @return bool
     */
    static function isVector(&$ar)
    {
        reset($ar);
        list($k) = each($ar);
        return $k === 0;
    }

    /**
     * 少数/整数を表示用にフォーマットする
     * 1234.123000 -> 1234.123
     * 1234567890 -> 1234567890
     *
     * @param string $val
     *
     * @return string
     */
    static function formatBigFloat(string $val): string
    {
        if (!preg_match('/\./', $val)) {
            return $val;
        }
        return preg_replace('/\.?0+$/', '', $val);
    }

    /**
     * 連想配列か判定
     * メモリ増加を防ぐ為参照渡しとする
     *
     * @param $ar
     *
     * @return bool
     */
    static function isHash(&$ar)
    {
        reset($ar);
        list($k) = each($ar);
        return $k !== 0;
    }

    /**
     * 日数の差分を求める(デフォルトで繰り上げ)
     * $targetTimeから$baseTimeの差
     *
     * @param int  $baseTimestamp
     * @param int  $targetTimestamp
     * @param bool $roundUp if false, round off
     *
     * @return int
     */
    static function diffDays(int $baseTimestamp, int $targetTimestamp, bool $roundUp = true): int
    {
        $days = ($targetTimestamp - $baseTimestamp) / DAY;
        if ($roundUp) {
            return ceil($days);
        }
        return round($days);
    }

    /**
     * Y-m-d 形式の日付を返す
     *
     * @param int $timestamp
     *
     * @return string
     */
    static function dateYmd(int $timestamp): string
    {
        return date('Y-m-d', $timestamp);
    }

    /**
     * 値が指定した範囲に含まれるか？
     *
     * @param int $target
     * @param int $start
     * @param int $end
     *
     * @return bool
     */
    static function between(int $target, int $start, int $end): bool
    {
        if ($target >= $start && $target <= $end) {
            return true;
        }
        return false;
    }
}
