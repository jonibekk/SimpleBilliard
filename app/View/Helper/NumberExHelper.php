<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Class NumberExHelper
 */
class NumberExHelper extends AppHelper
{
    /**
     * 数字を単位付きフォーマットに変換する
     * e.g.
     *   999         -> 999
     *   1200        -> 1.2K
     *   12000       -> 12K
     *   1200000     -> 1.2M
     *   12000000    -> 12M
     *   1200000000  -> 1.2G
     *   12000000000 -> 12G
     *
     * @param int   $num  フォーマットする数値
     * @param array $options
     *                    convert_start: int  $num がこの値より大きな数値の場合だけフォーマットする
     *
     * @return string
     */
    public function formatHumanReadable($num, $options = [])
    {
        $num = AppUtil::formatBigFloat($num);
        $options = array_merge(
            [
                'convert_start' => 0,
            ], $options);

        // convert_start よりも小さい場合は何もしない
        if ($num < $options['convert_start']) {
            return $num;
        }

        if ($num < 1000) {
            // pass
        } elseif ($num < 10000) {
            // "1.2K"
            $num = sprintf("%.1fK", round($num / 1000, 1, PHP_ROUND_HALF_DOWN));
        } elseif ($num < 1000000) {
            // "12K"
            $num = sprintf("%dK", floor($num / 1000));
        } elseif ($num < 10000000) {
            // "1.2M"
            $num = sprintf("%.1fM", round($num / 1000000, 1, PHP_ROUND_HALF_DOWN));
        } elseif ($num < 1000000000) {
            // "12M"
            $num = sprintf("%dM", floor($num / 1000000));
        } elseif ($num < 10000000000) {
            // "1.2G"
            $num = sprintf("%.1fG", round($num / 1000000000, 1, PHP_ROUND_HALF_DOWN));
        } elseif ($num < 1000000000000) {
            // "12G"
            $num = sprintf("%dG", floor($num / 1000000000));
        }
        return $num;
    }

    /**
     * 進捗値フォーマット
     *
     * @param float $val
     * @param int   $unit
     * @param bool  $isEnd
     *
     * @return mixed|string
     */
    public function formatProgressValue(string $val, int $unit)
    {
        $val = AppUtil::formatThousand($val);
        if ($unit == KeyResult::UNIT_BINARY) {
            return !empty($val) ? __("Complete") : __("Incomplete");
        }

        $unitName = KeyResult::$UNIT[$unit];
        if (in_array($unit, KeyResult::$UNIT_HEAD)) {
            return $unitName.$val;
        }
        if (in_array($unit, KeyResult::$UNIT_TAIL)) {
            return $val.$unitName;
        }

        return $val;
    }

    /**
     * 進捗値短縮フォーマット
     *
     * @param float $val
     * @param int   $unit
     *
     * @return mixed|string
     */
    public function shortFormatProgressValue(float $val, int $unit)
    {
        if ($unit == KeyResult::UNIT_BINARY) {
            return !empty($val) ? __("Complete") : __("Incomplete");
        }
        $fmtVal = $this->formatHumanReadable(round($val));
        $unitName = KeyResult::$UNIT[$unit];
        if (in_array($unit, KeyResult::$UNIT_HEAD)) {
            return $unitName.$fmtVal;
        }
        if (in_array($unit, KeyResult::$UNIT_TAIL)) {
            return $fmtVal.$unitName;
        }

        return $fmtVal;
    }

    /**
     * 進捗率計算
     *
     * @param $start
     * @param $end
     * @param $current
     *
     * @return int
     */
    public function calcProgressRate(string $start, string $end, string $current) : int
    {
        if ($current == $end) {
            return 100;
        }
        // 分母
        $denominator = $end - $start;
        // 分子
        $numerator = $current - $start;
        // 小数点は切り捨て
        $rate = floor($numerator / $denominator * 100);
        if ($rate == 0 && $numerator > 0) {
            return 1;
        }
        return $rate;
    }

    /**
     * 単位付加
     *
     * @param string $val
     * @param int    $unitId
     *
     * @return string
     */
    public function addUnit(string $val, int $unitId) : string
    {
        // 単位を文頭におくか文末に置くか決める
        $unitName = KeyResult::$UNIT[$unitId];
        if (in_array($unitId, KeyResult::$UNIT_HEAD)) {
            return $unitName.$val;
        }
        if (in_array($unitId, KeyResult::$UNIT_TAIL)) {
            return $val.$unitName;
        }
        return $val;
    }

    public function addPlusIfOverLimit(int $number, int $limit)
    {
        if ($number > $limit) {
            return "${limit}+";
        }
        return $number;
    }
}
