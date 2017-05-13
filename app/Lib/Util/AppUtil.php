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
     * timezoneを考慮した開始日のtimestampを返す
     *
     * @param  string $dateStr
     * @param  float  $timezone
     *
     * @return int
     */
    static function getStartTimestampByTimezone(string $dateStr, float $timezone): int
    {
        return self::getTimestampByTimezone($dateStr . " 00:00:00", $timezone);
    }

    /**
     * timezoneを考慮した終了日のtimestampを返す
     *
     * @param  string $dateStr
     * @param  float  $timezone
     *
     * @return int
     */
    static function getEndTimestampByTimezone(string $dateStr, float $timezone): int
    {
        return self::getTimestampByTimezone($dateStr . " 23:59:59", $timezone);
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
        // 空の配列はhashとみなさない
        if (count($ar) === 0) {
            return false;
        }

        reset($ar);
        list($k) = each($ar);
        return $k !== 0;
    }

    /**
     * トータル日数を求める(デフォルトで繰り上げ) getting different date
     * $targetDateから$baseDateの差
     * - e.g.
     * -- $baseDate = '2017-01-01', $targetDate = '2017-01-01' の場合は、1
     * -- $baseDate = '2017-01-01', $targetDate = '2017-01-02' の場合は、2
     *
     * @param string $baseDate
     * @param string $targetDate
     * @param bool   $roundUp if false, round off
     *
     * @return int
     */
    static function totalDays(string $baseDate, string $targetDate, bool $roundUp = true): int
    {
        $days = (strtotime($targetDate) + DAY - strtotime($baseDate)) / DAY;
        if ($roundUp) {
            return ceil($days);
        }
        return round($days);
    }

    /**
     * 日数の差分を求める(デフォルトで繰り上げ) getting different date
     * $targetDateから$baseDateの差
     * - e.g.
     * -- $baseDate = '2017-01-01', $targetDate = '2017-01-01' の場合は、0
     * -- $baseDate = '2017-01-01', $targetDate = '2017-01-02' の場合は、1
     *
     * @param string $baseDate
     * @param string $targetDate
     * @param bool   $roundUp if false, round off
     *
     * @return int
     */
    static function diffDays(string $baseDate, string $targetDate, bool $roundUp = true): int
    {
        $days = (strtotime($targetDate) - strtotime($baseDate)) / DAY;
        if ($roundUp) {
            return ceil($days);
        }
        return round($days);
    }

    /**
     * Ymd 形式の日付を返す
     *
     * @param int    $timestamp
     * @param string $separator
     *
     * @return string
     */
    static function dateYmd(int $timestamp, string $separator = "-"): string
    {
        return date("Y{$separator}m{$separator}d", $timestamp);
    }

    /**
     * Ymd形式でdate型をリフォーマットする
     *
     * @param string $date
     * @param string $separator
     *
     * @return string
     */
    static function dateYmdReformat(string $date,string $separator):string
    {
        return date("Y{$separator}m{$separator}d", strtotime($date));
    }

    /**
     * Y-m-d 形式で対象日の前日の日付を返す
     *
     * @param string $targetDate
     *
     * @return string
     */
    static function dateYesterday(string $targetDate): string
    {
        return self::dateYmd(strtotime("$targetDate -1 day"));
    }

    /**
     * Y-m-d 形式で対象日の次の日の日付を返す
     *
     * @param string $targetDate
     *
     * @return string
     */
    static function dateTomorrow(string $targetDate): string
    {
        return self::dateYmd(strtotime("$targetDate +1 day"));
    }

    /**
     * Y-m-d 形式で対象日の○○日後の日付を返す
     *
     * @param string $targetDate
     * @param int    $days
     *
     * @return string
     */
    static function dateAfter(string $targetDate, int $days): string
    {
        return self::dateYmd(strtotime("$targetDate +{$days} days"));
    }

    /**
     * Y-m-d 形式で対象日の○○日前の日付を返す
     *
     * @param string $targetDate
     * @param int    $days
     *
     * @return string
     */
    static function dateBefore(string $targetDate, int $days): string
    {
        return self::dateYmd(strtotime("$targetDate -{$days} days"));
    }

    /**
     * Y-m-d 形式のローカルの日付を返す
     *
     * @param int $timestamp
     * @param int $timezone
     *
     * @return string
     */
    static function dateYmdLocal(int $timestamp, float $timezone): string
    {
        return self::dateYmd($timestamp + $timezone * HOUR);
    }

    /**
     * 今日のY-m-d 形式のローカルの日付を返す
     *
     * @param int $timezone
     *
     * @return string
     */
    static function todayDateYmdLocal(float $timezone)
    {
        return self::dateYmd(REQUEST_TIMESTAMP + $timezone * HOUR);
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

    /**
     * 小数点以下の桁数を指定して切り捨てる
     *
     * @param float $value
     * @param int   $numDecimalPlace
     *
     * @return float
     */
    static function floor(float $value, int $numDecimalPlace = 1): float
    {
        $tmpNum = pow(10, $numDecimalPlace);
        return floor($value * $tmpNum) / $tmpNum;
    }

    /**
     * 時差の時(指定分刻み)を求める。
     * 指定分が30分の場合で、時差が1:29なら1.0, 1:30なら1.5, 1:59なら1.5
     *
     * @param int $targetTimestamp
     * @param int $baseTimestamp
     * @param int $borderMinute
     *
     * @return float
     */
    static function diffHourFloorByMinute(int $targetTimestamp, int $baseTimestamp, int $borderMinute = 30): float
    {
        $baseDate = new DateTime(date('Y-m-d H:i:s', $baseTimestamp));
        $diff = $baseDate->diff(new DateTime(date('Y-m-d H:i:s', $targetTimestamp)));
        //指定分が30の場合、29分なら0, 59分なら30
        $flooredMinute = floor($diff->i / $borderMinute) * $borderMinute;
        //30分なら0.5
        $minuteConvertedToHour = $flooredMinute / 60;
        $diffHour = $diff->h + $minuteConvertedToHour;
        return $diffHour;
    }

    /**
     * タイムゾーン一覧を取得
     * key:timezone, value:description
     *
     * @return array
     */
    static function getTimezoneList(): array
    {
        $timezones = [
            '-12.0' => '(GMT -12:00 hours) Eniwetok, Kwajalein',
            '-11.0' => '(GMT -11:00 hours) Midway Island, Somoa',
            '-10.0' => '(GMT -10:00 hours) Hawaii',
            '-9.0'  => '(GMT -9:00 hours) Alaska',
            '-8.0'  => '(GMT -8:00 hours) Pacific Time (US & Canada)',
            '-7.0'  => '(GMT -7:00 hours) Mountain Time (US & Canada)',
            '-6.0'  => '(GMT -6:00 hours) Central Time (US & Canada), Mexico City',
            '-5.0'  => '(GMT -5:00 hours) Eastern Time (US & Canada), Bogota, Lima, Quito',
            '-4.0'  => '(GMT -4:00 hours) Atlantic Time (Canada), Caracas, La Paz',
            '-3.5'  => '(GMT -3:30 hours) Newfoundland',
            '-3.0'  => '(GMT -3:00 hours) Brazil, Buenos Aires, Georgetown',
            '-2.0'  => '(GMT -2:00 hours) Mid-Atlantic',
            '-1.0'  => '(GMT -1:00 hours) Azores, Cape Verde Islands',
            '0.0'   => '(GMT) Western Europe Time, London, Lisbon, Casablanca, Monrovia',
            '+1.0'  => '(GMT +1:00 hours) CET(Central Europe Time), Brussels, Copenhagen, Madrid, Paris',
            '+2.0'  => '(GMT +2:00 hours) EET(Eastern Europe Time), Kaliningrad, South Africa',
            '+3.0'  => '(GMT +3:00 hours) Baghdad, Kuwait, Riyadh, Moscow, St. Petersburg, Volgograd, Nairobi',
            '+3.5'  => '(GMT +3:30 hours) Tehran',
            '+4.0'  => '(GMT +4:00 hours) Abu Dhabi, Muscat, Baku, Tbilisi',
            '+4.5'  => '(GMT +4:30 hours) Kabul',
            '+5.0'  => '(GMT +5:00 hours) Ekaterinburg, Islamabad, Karachi, Tashkent',
            '+5.5'  => '(GMT +5:30 hours) Bombay, Calcutta, Madras, New Delhi',
            '+6.0'  => '(GMT +6:00 hours) Almaty, Dhaka, Colombo',
            '+7.0'  => '(GMT +7:00 hours) Bangkok, Hanoi, Jakarta',
            '+8.0'  => '(GMT +8:00 hours) Beijing, Perth, Singapore, Hong Kong, Chongqing, Urumqi, Taipei',
            '+9.0'  => '(GMT +9:00 hours) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
            '+9.5'  => '(GMT +9:30 hours) Adelaide, Darwin',
            '+10.0' => '(GMT +10:00 hours) EAST(East Australian Standard), Guam, Papua New Guinea, Vladivostok',
            '+11.0' => '(GMT +11:00 hours) Magadan, Solomon Islands, New Caledonia',
            '+12.0' => '(GMT +12:00 hours) Auckland, Wellington, Fiji, Kamchatka, Marshall Island'
        ];
        return $timezones;
    }

    /**
     * クライアントのタイムゾーンを算出
     *
     * @param $clientDatetime
     *
     * @return int|null|string
     */
    static function getClientTimezone(string $clientDatetime)
    {
        if (!$clientDatetime) {
            return null;
        }
        $clientTimestamp = strtotime($clientDatetime);
        $serverTimestamp = strtotime(date('Y-m-d H:i:s'));
        $diff = $clientTimestamp - $serverTimestamp;
        $hour = ($diff / 60 / 60);
        $timezones = self::getTimezoneList();
        //配列の先頭
        $first = array_slice($timezones, 0, 1);
        //配列の最後
        $end = array_slice($timezones, -1, 1);
        $prevKey = null;
        foreach ($timezones as $key => $value) {
            if ($hour <= $key) {
                if ($key == key($first)) {
                    return $key;
                }
                if ($key == key($end)) {
                    return $key;
                }
                if (($hour - $prevKey) >= ($key - $hour)) {
                    return $key;
                } else {
                    return $prevKey;
                }
            }
            $prevKey = $key;
        }
        return null;
    }

    /**
     * filtering by whiteList
     *
     * @param array $targetArray
     * @param array $whiteListKeys e.g. ['id','name'...]
     *
     * @return array
     */
    static function filterWhiteList(array $targetArray, array $whiteListKeys): array
    {
        return array_intersect_key($targetArray, array_flip($whiteListKeys));
    }

    /**
     * getting base command of back ground job
     *
     * @return string
     */
    static function baseCmdOfBgJob(): string
    {
        $nohup = "nohup ";
        $php = '/opt/phpbrew/php/php-' . phpversion() . '/bin/php ';
        $cakeCmd = $php . APP . "Console" . DS . "cake.php";
        $cakeApp = " -app " . APP;
        $baseCommand = $nohup . $cakeCmd . $cakeApp;
        return $baseCommand;
    }

    /**
     * flatten multi dimentions
     *  - with being unique
     *  - with unset empty value
     *  - with reassign array keys
     *
     * @param  array $multiDimentions
     *
     * @return array
     */
    static function flattenUnique(array $multiDimentions): array
    {
        $flattened = Hash::flatten($multiDimentions);
        $filterd = Hash::filter($flattened);
        $uniqued = array_unique($filterd);
        $keyReassigned = array_values($uniqued);
        return $keyReassigned;
    }

    /**
     * adding query params to url
     *
     * @param string $baseUrl
     * @param array  $params ["key1"=>"val1","key2"=>"val2"]
     *
     * @return string
     */
    static function addQueryParamsToUrl(string $baseUrl, array $params): string
    {
        // e.g. $parsedUrl has ["scheme"=>"http","host"=>"xxx.co.jp","path"=>"/to/path","query"=>"key1=var1&key2=val2"]
        $parsedUrl = parse_url($baseUrl);
        // e.g. $parsedQuery will have ["key1"=>"val1","key2"=>"val2"]
        $parsedQuery = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parsedQuery);
        }
        // merge queries.
        $parsedQuery = am($parsedQuery, $params);
        $parsedUrl['query'] = http_build_query($parsedQuery);
        return self::unparseUrl($parsedUrl);
    }

    /**
     * unparsing parsed url to string url
     *
     * @param array $parsedUrl parsed by using parse_url()
     *
     * @return string
     */
    static function unparseUrl(array $parsedUrl): string
    {
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    static function rangeYmI18n(string $startYm, string $endYm): array
    {
        $startYm = date('Y-m', strtotime($startYm));
        $endYm = date('Y-m', strtotime($endYm));
        $range = [];

        if ($startYm > $endYm) {
            return $range;
        }

        // 20 year is realistic upper limit...
        for ($add = 0; $add < 240; $add++) {
            $newYmTimeStamp = strtotime("$startYm +$add month");
            $newYm = date("Y-m", $newYmTimeStamp);
            $range[$newYm] = self::formatYmI18n($newYmTimeStamp);
            if ($newYm === $endYm) {
                break;
            }
        }
        return $range;
    }

    static function formatYmI18n(int $time): string
    {
        switch (Configure::read('Config.language')) {
            case "jpn":
                $formattedYm = date("Y年m月", $time);
                break;
            default:
                $formattedYm = date("M Y", $time);
        }
        return $formattedYm;
    }
}
