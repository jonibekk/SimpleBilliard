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
     * @param       $num  フォーマットする数値
     * @param array $options
     *                    convert_start: int  $num がこの値より大きな数値の場合だけフォーマットする
     *
     * @return string
     */
    public function formatHumanReadable($num, $options = [])
    {
        $options = array_merge(
            [
                'convert_start' => 0,
            ], $options);

        if ($options['convert_start'] <= $num && $num < 1000) {
            // pass
        }
        elseif ($options['convert_start'] <= $num && $num < 10000) {
            // "1.2K"
            $num = sprintf("%.1fK", round($num / 1000, 1, PHP_ROUND_HALF_DOWN));
        }
        elseif ($options['convert_start'] <= $num && $num < 1000000) {
            // "12K"
            $num = sprintf("%dK", floor($num / 1000));
        }
        elseif ($options['convert_start'] <= $num && $num < 10000000) {
            // "1.2M"
            $num = sprintf("%.1fM", round($num / 1000000, 1, PHP_ROUND_HALF_DOWN));
        }
        elseif ($options['convert_start'] <= $num && $num < 1000000000) {
            // "12M"
            $num = sprintf("%dM", floor($num / 1000000));
        }
        elseif ($options['convert_start'] <= $num && $num < 10000000000) {
            // "1.2G"
            $num = sprintf("%.1fG", round($num / 1000000000, 1, PHP_ROUND_HALF_DOWN));
        }
        elseif ($options['convert_start'] <= $num && $num < 1000000000000) {
            // "12G"
            $num = sprintf("%dG", floor($num / 1000000000));
        }
        return $num;
    }

}
