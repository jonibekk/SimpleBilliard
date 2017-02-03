<?php
App::uses('AppModel', 'Model');

/**
 * KrValuesDailyLog Model
 */
class KrValuesDailyLog extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'current_value' => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'start_value'   => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'target_value'  => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'priority'      => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'       => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
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
            'fields'     => ['goal_id', 'key_result_id', 'current_value']
        ];

        $ret = $this->find('all', $options);
        $ret = Hash::extract($ret, '{n}.KrValuesDailyLog');
        return $ret;
    }

}
