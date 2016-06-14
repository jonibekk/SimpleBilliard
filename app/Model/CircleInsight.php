<?php
App::uses('AppModel', 'Model');

/**
 * CircleInsight Model
 *
 * @property Team   $Team
 * @property Circle $Circle
 */
class CircleInsight extends AppModel
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
        'Circle',
    ];

    /**
     * サークルの集計データを返す
     *
     * @param int    $circle_id
     * @param string $start_date 集計開始日 YYYY-MM-DD
     * @param string $end_date   集計終了日 YYYY-MM-DD
     * @param int    $timezone   time offset
     *
     * @return array|null
     */
    public function getTotal($circle_id, $start_date, $end_date, $timezone)
    {
        $options = [
            'fields'     => [
                'MAX(CircleInsight.user_count) as max_user_count',
            ],
            'conditions' => [
                'CircleInsight.team_id'        => $this->current_team_id,
                'CircleInsight.circle_id'      => $circle_id,
                'CircleInsight.target_date >=' => $start_date,
                'CircleInsight.target_date <=' => $end_date,
                'CircleInsight.timezone'       => $timezone,
            ],
        ];
        $res = $this->find('first', $options);
        $total = 0;
        if (!empty($res) && !empty($res[0]['max_user_count'])) {
            $total = $res[0]['max_user_count'];
        }
        return $total;
    }
}
