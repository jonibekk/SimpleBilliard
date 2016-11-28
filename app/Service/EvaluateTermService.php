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
        $data['start_date'] = date('Y-m-d', $data['start_date'] + $data['timezone'] * HOUR);
        $data['end_date'] = date('Y-m-d', $data['end_date'] + $data['timezone'] * HOUR);
        return $data;
    }
}
