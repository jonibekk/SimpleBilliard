<?php
App::import('Service', 'AppService');

/**
 * Class EvaluateTermService
 */
class EvaluateTermService extends AppService
{
    /**
     * EvaluateTermデータをレスポンス用に整形
     *
     * @param  array  $data
     * @param  string $type
     *
     * @return array
     */
    function processEvaluateTerm(array $data, string $type): array
    {
        $data['type'] = $type;
        $data['start_date'] = $this->regenerateDateByTimezone($data['start_date'], $data['timezone']);
        $data['end_date'] = $this->regenerateDateByTimezone($data['end_date'], $data['timezone']);
        return $data;
    }

    /**
     * timezoneを元にdate文字列を再生成
     *
     * @param  int $date
     * @param  int $timezone
     *
     * @return string
     */
    function regenerateDateByTimezone(int $date, int $timezone): string
    {
        $formatted = date('Y-m-d', $date + $timezone * HOUR);
        return $formatted;
    }
}
