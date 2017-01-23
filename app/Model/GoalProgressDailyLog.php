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
     * @param string $startDate Y-m-d
     * @param string $endDate   Y-m-d
     * @param array  $goalIds
     *
     * @return array
     */
    function findLogs(string $startDate, string $endDate, array $goalIds): array
    {
        $options = [
            'conditions' => [
                'goal_id'                     => $goalIds,
                'target_date BETWEEN ? AND ?' => [$startDate, $endDate],
            ],
            'order'      => ['target_date'],
            'fields'     => ['goal_id', 'progress', 'target_date']
        ];

        $ret = $this->find('all', $options);
        $ret = Hash::extract($ret, '{n}.GoalProgressDailyLog');
        return $ret;
    }

}
