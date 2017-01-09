<?php
App::uses('AppModel', 'Model');

/**
 * GoalProgressDailyLog Model
 */
class GoalProgressDailyLog extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'progress'    => [
            'numBetween' => [
                'rule' => ['numBetween', 0, 100],
            ],
            'numeric'    => [
                'rule' => ['numeric'],
            ],
        ],
        'target_date' => [
            'date' => [
                'rule' => ['date'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
    ];

    /**
     * ゴールidと日付範囲を元にログデータを取得
     *
     * @param string $start Y-m-d
     * @param string $end   Y-m-d
     * @param array  $goalIds
     *
     * @return array
     */
    function findLogs(string $start, string $end, array $goalIds): array
    {
        $options = [
            'conditions' => [
                'goal_id'                     => $goalIds,
                'target_date BETWEEN ? AND ?' => [$start, $end],
            ],
            'order'      => ['target_date'],
            'fields'     => ['goal_id', 'progress', 'target_date']
        ];

        $ret = $this->find('all', $options);
        $ret = Hash::extract($ret, '{n}.GoalProgressDailyLog');
        return $ret;
    }

}
