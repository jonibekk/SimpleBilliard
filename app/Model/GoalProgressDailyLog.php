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
     * TODO: 未実装
     * ゴールidを元にログデータを取得
     *
     * @param string $start
     * @param string $end
     * @param array  $goalIds
     *
     * @return array
     */
    function findLogs(string $start, string $end, array $goalIds): array
    {

    }

}
