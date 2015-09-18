<?php
App::uses('AppModel', 'Model');

/**
 * TeamInsight Model
 *
 * @property Team $Team
 */
class TeamInsight extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_count' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'    => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
    ];

    /**
     * $date の日付の週の開始日付と終了日付を返す
     *
     * @param $date
     * @param $params
     *               offset: $date の日付の週から offset 分ずらした週を返す
     *               1 を指定すると、$date の週の次週
     *               -1 を指定すると、$date の週の前週
     *
     * @return array|bool
     *               e.g. ['start' => '2015-09-07', 'end' => '2015-09-13']
     */
    public function getWeekRangeDate($date, $params = [])
    {
        $params = array_merge(['offset' => 0], $params);

        $time = strtotime("$date 00:00:00");
        if (!$time) {
            return false;
        }
        if ($params['offset']) {
            $time += WEEK * $params['offset'];
        }
        // 月曜日を 0 にした曜日
        $wday = date('N', $time) - 1;
        $week_start_time = $time - DAY * $wday;
        $week_end_time = $week_start_time + DAY * 6;
        return [
            'start' => date('Y-m-d', $week_start_time),
            'end'   => date('Y-m-d', $week_end_time),
        ];
    }

    /**
     * $date の日付の月の開始日付と終了日付を返す
     *
     * @param $date
     * @param $params
     *               offset: $date の日付の月から offset 分ずらした月を返す
     *               1 を指定すると、$date の月の次月
     *               -1 を指定すると、$date の月の前月
     *
     * @return array|bool
     *               e.g. ['start' => '2015-09-01', 'end' => '2015-09-30']
     */
    public function getMonthRangeDate($date, $params = [])
    {
        $params = array_merge(['offset' => 0], $params);

        $time = strtotime("$date 00:00:00");
        if (!$time) {
            return false;
        }
        $y_m = substr($date, 0, 7);

        if ($params['offset']) {
            $adjust = $params['offset'] > 0 ? '+' . intval($params['offset']) : $params['offset'];
            $time = strtotime("$y_m-01 $adjust month");
            $y_m = date('Y-m', $time);
        }

        return [
            'start' => "$y_m-01",
            'end'   => "$y_m-" . date('t', $time),
        ];
    }

    /**
     * $start_date から $end_date の集計結果を返す
     *
     * @param $start_date
     * @param $end_date
     * @param $timezone
     *
     * @return array|null
     */
    public function getTotal($start_date, $end_date, $timezone)
    {
        $options = [
            'fields'     => [
                'MAX(user_count) as max_user_count',
            ],
            'conditions' => [
                'TeamInsight.team_id'        => $this->current_team_id,
                'TeamInsight.target_date >=' => $start_date,
                'TeamInsight.target_date <=' => $end_date,
                'TeamInsight.timezone'       => $timezone,
            ],
        ];
        return $this->find('first', $options);
    }
}
