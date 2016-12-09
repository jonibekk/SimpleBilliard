<?php
App::import('Service', 'AppService');

/**
 * Class EvaluateTermService
 */
class EvaluateTermService extends AppService
{
    /**
     * EvaluteTermデータをレスポンス用に整形
     *
     * @param  $data
     * @param  $type
     *
     * @return [type]       [description]
     */
    function processEvaluateTerm($data, $type)
    {
        $data['type'] = $type;
        $data['start_date'] = $this->regenerateDateByTimezone($data['start_date'], $data['timezone']);
        $data['end_date'] = $this->regenerateDateByTimezone($data['end_date'], $data['timezone']);
        return $data;
    }

    /**
     * timezoneを元にdate文字列を再生成
     * @param  $date
     * @param  $timezone
     * @return $formatted
     */
    function regenerateDateByTimezone($date, $timezone) {
        $formatted = date('Y-m-d', $date + $timezone * HOUR);
        return $formatted;
    }
}
